<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\UserRepository;
use App\Http\Requests\Auth\QuotaRequest;
use App\Http\Requests\Admin\AdminRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Http\Controllers\Controller;
use App\Services\RequestApiService;
use App\Services\CephService;

use JWTAuth;
use Aws\S3\S3Client;

class AdminController extends Controller
{
    protected $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
        $this->user = JWTAuth::parseToken()->authenticate();
        $this->ceph = new CephService(env('ServerIP'), env('Username'), env('Port'), env('PublicKeyPath'), env('PrivateKeyPath'));
    }

    public function index($page, RequestApiService $requestApiService)
    {
        if ($this->user['role'] != 'admin') {
            return response()->json(['message' => 'Permission denied'], 403);
        }
        if ($page < 1) {
            return response()->json(['message' => 'The page value is not incorrect'], 403);
        }
        $listUser = $this->users->getUser($page, 10);
        if (count($listUser) == 0) {
            $userState['users'] = $listUser;
        } else {
            $userState = $this->ceph->listStatus($listUser, $requestApiService);
        }
        $userState['count'] = $this->users->getUserCount();
        return response()->json($userState, 200);
    }

    public function create(AdminRequest $request, RequestApiService $requestApiService)
    {
        $user = $this->user;
        if ($user['role'] != 'admin') {
            return response()->json(['message' => 'Permission denied'], 403);
        }
        $data = $request->all();
        $data['uid'] = $data['email'];
        $data['name'] = $data['email'];
        $httpQuery = http_build_query([
            'uid' => $data['email'],
            'display-name' => $data['email'],
            'email' => $data['email']
        ]);
        if ($this->users->check($data['email'])) {
            return response()->json(['message' => 'The email has already been taken.'], 403);
        }
        $result = json_decode($requestApiService->request('PUT', 'user', "?format=json&$httpQuery"));
        if ($result) {
            $data['access_key'] = $result->keys[0]->access_key;
            $data['secret_key'] = $result->keys[0]->secret_key;
            $resultData = $this->users->createUser($data);
            return response()->json($resultData);
        }
        return response()->json(['message' => 'curl_has_error'], 401);
    }

    public function reset(AdminRequest $request)
    {
        $user = $this->user;
        if ($user['role'] != 'admin') {
            return response()->json(['message' => 'Permission denied'], 403);
        }
        $data = $request->all();
        if (!$this->users->check($data['email'])) {
            return response()->json(['message' => 'The email does not exist.'], 403);
        }
        $resultData = $this->users->resetPassword($data);
        return response()->json(['Users' => $resultData], 200);
    }

    public function update(UpdateRoleRequest $request)
    {
        $user = $this->user;
        if ($user['role'] != 'admin') {
            return response()->json(['message' => 'Permission denied'], 403);
        }
        $data = $request->all();
        if ($data['email'] == 'root@inwinstack.com') {
            return response()->json(['message' => 'Root is not allowed to be operated.'], 405);
        }
        if (!$this->users->check($data['email'])) {
            return response()->json(['message' => 'The email does not exist.'], 403);
        }
        $resultData = $this->users->updateRole($data);
        return response()->json(['Users' => $resultData], 200);
    }

    public function destroy(RequestApiService $requestApiService, $email)
    {
        $user = $this->user;
        if ($user['role'] != 'admin') {
            return response()->json(['message' => 'Permission denied'], 403);
        }
        if ($email == 'root@inwinstack.com') {
            return response()->json(['message' => 'Root is not allowed to be operated.'], 405);
        }
        $httpQuery = http_build_query([
            'uid' => $email,
            'purge-data' => true
        ]);
        if ($this->users->check($email)) {
            $result = json_decode($requestApiService->request('DELETE', 'user', "?format=json&$httpQuery"));
            $resultData = $this->users->removeUser($email);
            if ($resultData) {
                return response()->json(['message' => 'The user has been deleted.'], 200);
            }
            return response()->json(['message' => 'The delete user operation failed.'], 403);
        }
        return response()->json(['message' => 'The email does not exist.'], 403);
    }

    public function setUserQuota(QuotaRequest $request, RequestApiService $requestApiService)
    {
        $user = $this->user;
        if ($user['role'] != 'admin') {
            return response()->json(['message' => 'Permission denied'], 403);
        }
        $data = $request->all();
        if ($data['maxSizeKB'] < -1) {
            return response()->json(['message' => 'Max Size are not allowed'], 403);
        }
        if (!$this->users->check($data['email'])) {
            return response()->json(['message' => 'The user is not exist'], 403);
        }
        $totalCapacity = $this->ceph->totalCapacity();
        if ($data['maxSizeKB'] > $totalCapacity / 1024) {
            return response()->json(['message' => 'Max size is bigger than variable capacity']);
        }
        $httpQuery = http_build_query([
            'bucket' => -1,
            'max-objects' => -1,
            'max-size-kb' => $data['maxSizeKB'],
            'quota-scope' => 'user',
            'enabled' => $data['enabled']
        ]);
        $result = json_decode($requestApiService->request('PUT', 'user', "?quota&uid=" . $data['email'] . "&quota-type=user&$httpQuery"));
        return response()->json(['message' => 'Setting is successful'], 200);
    }
}
