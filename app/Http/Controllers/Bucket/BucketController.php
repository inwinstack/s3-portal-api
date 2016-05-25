<?php

namespace App\Http\Controllers\Bucket;

use App\Services\BucketService;

use App\Http\Requests\Bucket\BucketRequest;
use App\Http\Controllers\Controller;

use JWTAuth;
use Aws\S3\S3Client;

class BucketController extends Controller
{
    protected $s3Service;
    protected $user;

    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $this->s3Service = new BucketService($this->user['access_key'], $this->user['secret_key']);
    }

    public function checkBucket($userBucket)
    {
        return $this->s3Service->checkBucket($userBucket);
    }

    public function index()
    {
        $listResponse = $this->s3Service->listBucket();
        return response()->json(['Buckets' => $listResponse->get('Buckets')], 200);
    }

    public function store(BucketRequest $request)
    {
        $checkBucket = $this->checkBucket($request->bucket);

        if ($checkBucket) {
            return response()->json(['message' => 'Has Bucket'], 403);
        }

        $bucketResponse = $this->s3Service->createBucket($request->bucket);

        if ($bucketResponse) {
            return $this->index();
        }

        return response()->json(['message' => 'Create Bucket Error'], 403);
    }

    public function destroy(BucketRequest $request)
    {
        $checkBucket = $this->checkBucket($request->bucket);

        if (!$checkBucket) {
            return response()->json(['message' => 'Bucket Non-exist'], 403);
        }

        $bucketResponse = $this->s3Service->deleteBucket($request->bucket);
        if ($bucketResponse) {
            return response()->json(['message' => 'Delete Bucket Success'], 200);
        }
        return response()->json(['message' => 'Delete Bucket Error'], 403);
    }
}
