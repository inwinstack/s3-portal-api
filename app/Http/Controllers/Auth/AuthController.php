<?php

namespace App\Http\Controllers\Auth;

use App\Repositories\UserRepository;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\CheckEmailRequest;
use App\Http\Requests\Auth\QuotaRequest;
use App\Services\RequestApiService;
use App\Services\CephService;

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
        if ($this->users->check($data['email'])) {
            return response()->json(['message' => 'The user is exist'], 403);
        }
        $httpQuery = http_build_query([
            'uid' => $data['uid'],
            'display-name' => $data['email'],
            'email' => $data['email'],
            'user-caps' => 'usage=read, write; users=read'
        ]);
        $result = json_decode($requestApiService->request('PUT', 'user', "?format=json&$httpQuery"));
        $httpQuery = http_build_query([
          'bucket' => '-1',
          'max-objects' => '-1',
          'max-size-kb' => env('USER_DEFAULT_CAPACITY_KB'),
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
        $data = $this->users->verify($request->all());
        if ($data) {
            $data['token'] = JWTAuth::fromUser($data);
            return response()->json($data, 200);
        } else {
            return response()->json(['message' => 'The email is not exist'], 401);
        }
    }

    public function checkEmail($email)
    {
        if ($this->users->check($email)) {
            return response()->json(['message' => 'The email is exist'], 403);
        } else {
            return response()->json(['message' => 'You can use the email'], 200);
        }
    }

    public function logout()
    {
        $token = JWTAuth::getToken();
        if (JWTAuth::setToken($token)->invalidate()) {
            return response()->json(['message' => 'Logout is successfully'], 200);
        } else {
            return response()->json(['message' => 'Logout is failed'], 401);
        }
    }

    public function checkCephConnected(RequestApiService $requestApiService)
    {
        if (!is_array(json_decode($requestApiService->request('GET', 'bucket', "?format=json")))) {
            return response()->json(['message' => 'Connection to Ceph failed'], 403);
        } else {
            return  response()->json(['message' => 'Connected to Ceph success'], 200);
        }
    }

    public function getUserQuota($user, RequestApiService $requestApiService)
    {
        if ($this->users->check($user)) {
            $result = json_decode($requestApiService->request('GET', 'user', "?quota&uid=" . $user . "&quota-type=user"));
            return response()->json($result, 200);
        } else {
            return response()->json(['message' => 'The user is not exist'], 403);
        }
    }
}
