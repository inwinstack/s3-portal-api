<?php

namespace App\Http\Controllers\Bucket;

use App\Services\BucketService;

use App\Http\Requests\Bucket\BucketRequest;
use App\Http\Controllers\Controller;

use JWTAuth;
use Aws\S3\S3Client;

class BucketController extends Controller
{
    protected $bucketService;
    protected $user;

    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $this->bucketService = new BucketService($this->user['access_key'], $this->user['secret_key']);
    }

    public function index()
    {
        $response = $this->bucketService->get();
        if ($response) {
            return response()->json(['Buckets' => $response->get('Buckets')], 200);
        } else {
            return response()->json(['message' => 'List bucket is failed'], 403);
        }
    }

    public function store(BucketRequest $request)
    {
        if (!preg_match("/[A-Z]/", $request->bucket)) {
            return response()->json(['message' => 'The bucket name is invalid'], 403);
        }
        if ($this->bucketService->exist($request->bucket)) {
            return response()->json(['message' => 'The bucket name is exist'], 403);
        }
        if ($this->bucketService->create($request->bucket) && $this->bucketService->cors($request->bucket)) {
            return $this->index();
        } else {
            return response()->json(['message' => 'Create bucket is failed'], 403);
        }
    }

    public function destroy($bucket)
    {
        if (!$this->bucketService->check($bucket)) {
            return response()->json(['message' => 'The Bucket is not exist'], 403);
        }
        if ($this->bucketService->delete($bucket)) {
            return response()->json(['message' => 'Delete bucket is successfully'], 200);
        } else {
            return response()->json(['message' => 'Delete bucket is failed'], 403);
        }
    }
}
