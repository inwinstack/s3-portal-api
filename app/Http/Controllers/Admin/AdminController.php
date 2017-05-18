<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\UserRepository;
use App\Http\Requests\Auth\QuotaRequest;
use App\Http\Requests\Admin\AdminRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Http\Controllers\Controller;
use App\Services\RequestApiService;
use App\Services\CephService;
use App\Services\AdminService;

use JWTAuth;
use Aws\S3\S3Client;

class AdminController extends Controller
{
    protected $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
        $this->user = JWTAuth::parseToken()->authenticate();
        $this->admin = new AdminService($this->user['access_key'], $this->user['secret_key']);
        $this->ceph = new CephService();
    }

    public function index($page, $count, RequestApiService $requestApiService)
    {
        if ($this->user['role'] != 'admin') {
            return response()->json(['message' => 'Permission denied'], 403);
        }
        if ($page < 1) {
            return response()->json(['message' => 'The page value is not incorrect'], 403);
        }
        if ($count < 1) {
            return response()->json(['message' => 'The count value is not incorrect'], 403);
        }
        $listUser = $this->users->getUser($page, $count);
        if (count($listUser) == 0) {
            $userState['users'] = $listUser;
        } else {
            $userState['users'] = $this->ceph->listStatus($listUser, $requestApiService);
        }
        return response()->json($userState, 200);
    }

    public function create(AdminRequest $request, RequestApiService $requestApiService)
    {
        if ($this->user['role'] != 'admin') {
            return response()->json(['message' => 'Permission denied'], 403);
        }
        if ($this->users->check($request->all()['email'])) {
            return response()->json(['message' => 'The user is exist'], 403);
        }
        $result = $this->admin->create($request->all(), $requestApiService);
        if ($result) {
            return response()->json($this->users->createUser($result));
        } else {
            return response()->json(['message' => 'The admin create user is failed'], 401);
        }
    }

    public function reset(AdminRequest $request)
    {
        if ($this->user['role'] != 'admin') {
            return response()->json(['message' => 'Permission denied'], 403);
        }
        if (!$this->users->check($request->all()['email'])) {
            return response()->json(['message' => 'The user is not exist'], 403);
        }
        $result = $this->users->resetPassword($request->all());
        if ($result) {
            return response()->json($result, 200);
        } else {
            return response()->json(['message' => 'The admin reset password is failed'], 403);
        }
    }

    public function update(UpdateRoleRequest $request)
    {
        if ($this->user['role'] != 'admin') {
            return response()->json(['message' => 'Permission denied'], 403);
        }
        if ($request->all()['email'] == 'root@inwinstack.com') {
            return response()->json(['message' => 'The root is not allowed to be operated'], 405);
        }
        if (!$this->users->check($request->all()['email'])) {
            return response()->json(['message' => 'The user is not exist'], 403);
        }
        $result = $this->users->updateRole($request->all());
        if ($result) {
            return response()->json($result, 200);
        } else {
            return response()->json(['message' => 'The admin update role is failed'], 403);
        }
    }

    public function destroy(RequestApiService $requestApiService, $email)
    {
        if ($this->user['role'] != 'admin') {
            return response()->json(['message' => 'Permission denied'], 403);
        }
        if ($email == 'root@inwinstack.com') {
            return response()->json(['message' => 'The root is not allowed to be operated'], 405);
        }
        if (!$this->users->check($email)) {
            return response()->json(['message' => 'The user is not exist'], 403);
        }
        if ($this->admin->delete($email, $requestApiService)) {
            $this->users->removeUser($email);
            return response()->json(['message' => 'The delete is successfully'], 200);
        } else {
            return response()->json(['message' => 'The delete is failed'], 403);
        }
    }

    public function setQuota(QuotaRequest $request, RequestApiService $requestApiService)
    {
        if ($this->user['role'] != 'admin') {
            return response()->json(['message' => 'Permission denied'], 403);
        }
        if ($request->all()['maxSizeKB'] < -1) {
            return response()->json(['message' => 'Max Size are not allowed'], 403);
        }
        if (!$this->users->check($request->all()['email'])) {
            return response()->json(['message' => 'The user is not exist'], 403);
        }
        $totalCapacity = $this->ceph->totalCapacity();
        if ($request->all()['maxSizeKB'] > $totalCapacity / 1024) {
            return response()->json(['message' => 'Max size is bigger than variable capacity'], 403);
        }
        $result = $this->admin->setQuota($request->all()['email'], $request->all()['maxSizeKB'], $request->all()['enabled'], $requestApiService);
        if ($result) {
            return response()->json(['message' => 'The setting is successfully'], 200);
        } else {
            return response()->json(['message' => 'The setting is failed'], 403);
        }
    }
}
