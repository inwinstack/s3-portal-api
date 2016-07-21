<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\UserRepository;
use App\Http\Requests\Admin\AdminRequest;
use App\Http\Controllers\Controller;
use App\Services\RequestApiService;

use JWTAuth;
use Aws\S3\S3Client;

class AdminController extends Controller
{
    protected $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index()
    {
        $user = $this->user;
        if($user['role'] != 'admin') {
            return response()->json(['message' => 'Permission denied'], 403);
        }
        $resultData = $this->users->getUsers();
        return response()->json(['Users' => $resultData], 200);
    }

    public function create(AdminRequest $request, RequestApiService $requestApiService)
    {
        $user = $this->user;
        if($user['role'] != 'admin') {
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
        $result = json_decode($requestApiService->request('PUT', 'user', "?format=json&$httpQuery"));

        if ($result) {
            $data['access_key'] = $result->keys[0]->access_key;
            $data['secret_key'] = $result->keys[0]->secret_key;
            if($this->users->check($data['email'])){
                return response()->json(['message' => 'The email has already been taken.'], 403);
            }
            $resultData = $this->users->createUser($data);
            return response()->json($resultData);
        }
        return response()->json(['message' => 'curl_has_error'], 401);
    }
}
