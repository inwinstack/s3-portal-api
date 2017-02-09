<?php

namespace App\Http\Controllers\Auth;

use App\Repositories\UserRepository;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\CheckEmailRequest;
use App\Http\Requests\Auth\QuotaRequest;
use App\Services\RequestApiService;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

use JWTAuth;

class AuthController extends Controller
{
    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * @var UserRepository
     */
    protected $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * register a user.
     *
     * @param RegisterRequest $request
     * @param RequestApiService $requestApiService
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request, RequestApiService $requestApiService)
    {
        $data = $request->all();
        $data['uid'] = $data['email'];
        $data['name'] = $data['email'];
        $httpQuery = http_build_query([
          'uid' => $data['uid'],
          'display-name' => $data['email'],
          'email' => $data['email'],
          'user-caps' => 'usage=read, write; users=read'
        ]);
        $result = json_decode($requestApiService->request('PUT', 'user', "?format=json&$httpQuery"));
        $httpQuery = http_build_query([
          'bucket' => '5',
          'max-objects' => '100',
          'max-size-kb' => '50000',
          'quota-scope' => 'user',
          'enabled' => true
        ]);
        if ($result) {
            $data['access_key'] = $result->keys[0]->access_key;
            $data['secret_key'] = $result->keys[0]->secret_key;
            $resultData = $this->users->createUser($data);
        }
        $updateQuotaResponse = json_decode($requestApiService->request('PUT', 'user', "?quota&uid=" . $data['email'] . "&quota-type=user&$httpQuery"));
        if ($result) {
            return response()->json($result, 200);
        } else {
            return response()->json(['message' => 'curl_has_error'], 401);
        }
    }

    public function login(LoginRequest $request, RequestApiService $requestApiService)
    {
        $result = json_decode($requestApiService->request('GET', 'bucket', "?format=json"));
        if (is_array($result) && sizeof($result) == 0) {
            return response()->json(['message' => 'Connection to Ceph failed'], 403);
        }
        $data = $this->users->verify($request->all());
        if ($data) {
            $data['token'] = JWTAuth::fromUser($data);
            return response()->json($data);
        }
        return response()->json(['message' => 'verify_error'], 401);
    }

    public function checkEmail($email)
    {
        $data = $this->users->check($email);
        if ($data) {
            return response()->json(['message' => 'has_user'], 403);
        }
        return response()->json(['message' => 'You can use the email']);
    }

    public function logout()
    {
        $token = JWTAuth::getToken();
        $invalidateResult = JWTAuth::setToken($token)->invalidate();
        if ($invalidateResult) {
            return response()->json(['message' => 'Invalidate Token Success']);
        }
        return response()->json(['message' => 'Invalidate Token Error'], 401);
    }

    public function checkCephConnected(RequestApiService $requestApiService)
    {
        $result = json_decode($requestApiService->request('GET', 'bucket', "?format=json"));
        if (is_array($result)) {
            return response()->json(['message' => 'Connection to Ceph failed'], 403);
        } else {
            return  response()->json(['message' => 'Connected to Ceph success'], 200);
        }
    }

    public function getUserQuota($user, RequestApiService $requestApiService)
    {
        $data = $this->users->check($user);
        if ($data) {
            $result = json_decode($requestApiService->request('GET', 'user', "?quota&uid=" . $user . "&quota-type=user"));
            return response()->json(['message' => $result], 200);
        } else {
            return response()->json(['message' => 'User is not exist'], 403);
        }
    }

    public function setUserQuota(QuotaRequest $request, RequestApiService $requestApiService)
    {
        $data = $request->all();
        if ($data['max-objects'] < -1 || $data['max-size-kb'] < -1) {
            return response()->json(['message' => 'Max Objects or Max Size are not allowed'], 403);
        }
        if ($data['bucket'] < 0) {
            return response()->json(['message' => 'The number of buckets must be positive'], 403);
        }
        if (!$this->users->check($data['email'])) {
            return response()->json(['message' => 'The user is not exist'], 403);
        }
        $httpQuery = http_build_query([
            'bucket' => $data['bucket'],
            'max-objects' => $data['max-objects'],
            'max-size-kb' => $data['max-size-kb'],
            'quota-scope' => 'user',
            'enabled' => $data['enabled']
        ]);
        $result = json_decode($requestApiService->request('PUT', 'user', "?quota&uid=" . $data['email'] . "&quota-type=user&$httpQuery"));
        return response()->json(['message' => 'Setting is successful'], 200);
    }

    public function getBucketQuota($user, RequestApiService $requestApiService)
    {
        $data = $this->users->check($user);
        if ($data) {
            $result = json_decode($requestApiService->request('GET', 'user', "?quota&uid=" . $user . "&quota-type=bucket"));
            return response()->json(['message' => $result], 200);
        } else {
            return response()->json(['message' => 'User is not exist'], 403);
        }
    }

    public function setBucketQuota(QuotaRequest $request, RequestApiService $requestApiService)
    {
        $data = $request->all();
        $httpQuery = http_build_query([
            'bucket' => $data['bucket'],
            'max-objects' => $data['max-objects'],
            'max-size-kb' => $data['max-size-kb'],
            'quota-scope' => 'bucket',
            'enabled' => $data['enabled']
        ]);
        $result = json_decode($requestApiService->request('PUT', 'user', "?quota&uid=" . $data['email'] . "&quota-type=bucket&$httpQuery"));
        return response()->json(['message' => 'Setting is successful'], 200);
    }
}
