<?php
namespace App\Services;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception as S3Exception;

class UserService extends S3Service
{
    protected $s3;

    public function __construct($accessKey, $secretKey)
    {
        $this->s3 = $this->connect($accessKey, $secretKey);
    }

    public function state($user, $buckets, $requestApiService, $cephService)
    {
        try {
            $sizeKB = 0;
            $objectCount = 0;
            $num = 0;
            $userQuota = json_decode($requestApiService->request('GET', 'user', "?quota&uid=" . $user . "&quota-type=user"));
            while ($num < count($buckets)) {
                $httpQuery = http_build_query([
                    'bucket' => $buckets[$num]['Name']
                ]);
                $stateResponse = json_decode($requestApiService->request('GET', 'bucket', "?$httpQuery"));
                if (!empty((array)($stateResponse->usage))) {
                    $sizeKB += $stateResponse->usage->{'rgw.main'}->size_kb;
                    $objectCount += $stateResponse->usage->{'rgw.main'}->num_objects;
                }
                $num++;
            }
            $result['total_size_kb'] = $sizeKB;
            if ($userQuota->max_size_kb == -1) {
                $result['max_size_kb'] = round($cephService->totalCapacity() / 1024);
            } else {
                $result['max_size_kb'] = $userQuota->max_size_kb;
            }
            $result['total_objects'] = $objectCount;
            $result['max_objects'] = $userQuota->max_objects;
            return $result;
        } catch (S3Exception $e) {
            return false;
        }
    }
}
