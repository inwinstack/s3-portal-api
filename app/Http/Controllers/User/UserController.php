<?php

namespace App\Http\Controllers\User;

use App\Services\BucketService;
use App\Services\RequestApiService;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use JWTAuth;

class UserController extends Controller
{
    protected $s3Service;
    protected $user;

    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $this->s3Service = new BucketService($this->user['access_key'], $this->user['secret_key']);
    }

    public function state($email, RequestApiService $requestApiService)
    {
        $sizeKB = 0;
        $objectCount = 0;
        $num = 0;
        $listResponse = $this->s3Service->listBucket();
        $bucketList = $listResponse->get('Buckets');
        $userQuota = json_decode($requestApiService->request('GET', 'user', "?quota&uid=" . $email . "&quota-type=user"));
        while ($num < count($bucketList)) {
            $httpQuery = http_build_query([
                'bucket' => $bucketList[$num]['Name']
            ]);
            $stateResponse = json_decode($requestApiService->request('GET', 'bucket', "?$httpQuery"));
            if (!empty((array)($stateResponse->usage))) {
                $sizeKB += $stateResponse->usage->{'rgw.main'}->size_kb;
                $objectCount += $stateResponse->usage->{'rgw.main'}->num_objects;
            }
            $num++;
        }
        if ($userQuota->max_size != -1) {
            $result['totalSizeKB'] = $sizeKB;
            $result['sizePercent'] = ($sizeKB / $userQuota->max_size_kb) * 100 . '%';
            $result['maxSizeKB'] = $userQuota->max_size_kb;
        } else {
            $result['totalSizeKB'] = -1;
            $result['sizePercent'] = -1;
            $result['maxSizeKB'] = -1;
        }
        if ($userQuota->max_objects != -1) {
            $result['totalObjects'] = $objectCount;
            $result['objectsPercent'] = ($objectCount / $userQuota->max_objects) * 100 . '%';
            $result['maxObjects'] = $userQuota->max_objects;
        } else {
            $result['totalObjects'] = -1;
            $result['objectsPercent'] = -1;
            $result['maxObjects'] = -1;
        }
        return response()->json($result, 200);
    }
}
