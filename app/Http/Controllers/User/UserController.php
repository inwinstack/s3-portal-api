<?php

namespace App\Http\Controllers\User;

use App\Services\RequestApiService;
use App\Services\BucketService;
use App\Services\CephService;
use App\Services\UserService;

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
        $this->bucketService = new BucketService($this->user['access_key'], $this->user['secret_key']);
        $this->userService = new UserService($this->user['access_key'], $this->user['secret_key']);
        $this->cephService = new CephService(env('ServerIP'), env('Username'), env('Port'), env('PublicKeyPath'), env('PrivateKeyPath'));
    }

    public function state(RequestApiService $requestApiService)
    {
        $result = $this->userService->state($this->user->uid, $this->bucketService->get()->get('Buckets'), $requestApiService, $this->cephService);
        if ($result) {
            return response()->json($result, 200);
        } else {
            return response()->json(['message' => 'Getting user state is failed'], 403);
        }
    }

    public function traffic($start, $end, RequestApiService $requestApiService)
    {
        $result = $this->userService->traffic($this->user->uid, $start, $end, $requestApiService);
        if ($result) {
            return response()->json(['traffic' => $result], 200);
        } else {
            return response()->json(['message' => 'Getting user traffic id failed'], 403);
        }
    }
}
