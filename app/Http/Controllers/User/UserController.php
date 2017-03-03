<?php

namespace App\Http\Controllers\User;

use App\Services\BucketService;
use App\Services\RequestApiService;
use App\Services\CephService;

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
        $this->ceph = new CephService(env('ServerIP'), env('Username'), env('Port'), env('PublicKeyPath'), env('PrivateKeyPath'));
    }

    public function state(RequestApiService $requestApiService)
    {
        $sizeKB = 0;
        $objectCount = 0;
        $num = 0;
        $listResponse = $this->s3Service->listBucket();
        $bucketList = $listResponse->get('Buckets');
        $userQuota = json_decode($requestApiService->request('GET', 'user', "?quota&uid=" . $this->user->uid . "&quota-type=user"));
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
        $result['total_size_kb'] = $sizeKB;
        if ($userQuota->max_size_kb == -1) {
            $result['max_size_kb'] = round($this->ceph->totalCapacity() / 1024);
        } else {
            $result['max_size_kb'] = $userQuota->max_size_kb;
        }
        $result['total_objects'] = $objectCount;
        $result['max_objects'] = $userQuota->max_objects;
        return response()->json($result, 200);
    }
}
