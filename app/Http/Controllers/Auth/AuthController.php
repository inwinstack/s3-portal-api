<?php

namespace App\Http\Controllers\Auth;

use App\Repositories\UserRepository;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\CheckEmailRequest;
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
        $data['uid'] = bcrypt($request->email.time());
        $data = array_merge($data, $request->all());

        $httpQuery = http_build_query([
            'uid' => $data['uid'],
            'display-name' => $data['name'],
            'email' => $data['email']
        ]);
        $result = json_decode($requestApiService->request('PUT', 'user', "?format=json&$httpQuery"));

        if ($result) {
            $data['access_key'] = $result->keys[0]->access_key;
            $data['secret_key'] = $result->keys[0]->secret_key;
            $resultData = $this->users->createUser($data);
            return response()->json($resultData);
        }
        return response()->json(['message' => 'curl_has_errot'], 401);
    }

    public function login(LoginRequest $request)
    {
        $data = $this->users->verify($request->all());
        if ($data) {
            $data['token'] = JWTAuth::fromUser($data);
            return response()->json($data);
        }
        return response()->json(['message' => 'verify_error'], 401);
    }

    public function checkEmail(CheckEmailRequest $request)
    {
        $data = $this->users->check($request->email);
        if ($data) {
            return response()->json(['message' => 'has_user'], 403);
        }
        return response()->json(['message' => 'GoodJob']);
    }
}
