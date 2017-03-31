<?php
namespace App\Services;

use App\Services\RequestApiService;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception as S3Exception;

class AdminService extends S3Service
{
    protected $s3;

    public function __construct($accessKey, $secretKey)
    {
        $this->s3 = $this->connect($accessKey, $secretKey);
    }

    public function create($user, $requestApiService)
    {
        try {
            $httpQuery = http_build_query([
                'uid' => $user['email'],
                'display-name' => $user['email'],
                'email' => $user['email']
            ]);
            $result = json_decode($requestApiService->request('PUT', 'user', "?format=json&$httpQuery"));
            $data['uid'] = $user['email'];
            $data['name'] = $user['email'];
            $data['email'] = $user['email'];
            $data['password'] = $user['password'];
            $data['access_key'] = $result->keys[0]->access_key;
            $data['secret_key'] = $result->keys[0]->secret_key;
            $httpQuery = http_build_query([
                'bucket' => '-1',
                'max-objects' => '-1',
                'max-size-kb' => env('UserDefaultCapacityKB'),
                'quota-scope' => 'user',
                'enabled' => true
            ]);
            json_decode($requestApiService->request('PUT', 'user', "?quota&uid=" . $data['email'] . "&quota-type=user&$httpQuery"));
            return $data;
        } catch (S3Exception $e) {
            return false;
        }
    }

    public function delete($email, $requestApiService)
    {
        try {
            $httpQuery = http_build_query([
                'uid' => $email,
                'purge-data' => true
            ]);
            json_decode($requestApiService->request('DELETE', 'user', "?format=json&$httpQuery"));
            return true;
        } catch (S3Exception $e) {
            return false;
        }
    }

    public function setQuota($email, $maxSizeKB, $enabled, $requestApiService)
    {
        try {
            $httpQuery = http_build_query([
                'bucket' => -1,
                'max-objects' => -1,
                'max-size-kb' => $maxSizeKB,
                'quota-scope' => 'user',
                'enabled' => $enabled
            ]);
            json_decode($requestApiService->request('PUT', 'user', "?quota&uid=" . $email . "&quota-type=user&$httpQuery"));
            return true;
        } catch (S3Exception $e) {
            return false;
        }
    }
}
