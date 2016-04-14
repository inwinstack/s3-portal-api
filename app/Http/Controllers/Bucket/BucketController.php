<?php

namespace App\Http\Controllers\Bucket;

use App\Services\S3Service;

use App\Http\Requests\Bucket\BucketRequest;
use App\Http\Controllers\Controller;

use JWTAuth;
use Aws\S3\S3Client;
class BucketController extends Controller
{
    protected $s3Service;
    protected $user;

    public function __construct(S3Service $s3Service)
    {
        $this->s3Service = $s3Service;
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function responseBucketName()
    {
        $listResponse = $this->s3Service->listBucket($this->user['access_key'], $this->user['secret_key']);
        return $listResponse->get('Buckets');
    }

    public function index()
    {
        return response()->json(['Buckets' => $this->responseBucketName()], 200);
    }

    public function store(BucketRequest $request)
    {
        $bucketResponse = $this->s3Service->createBucket($this->user['access_key'], $this->user['secret_key'], $request->bucket);
        if ($bucketResponse) {
            return $this->index();
        }
        return response()->json(['message' => 'Create Bucket Error'], 401);
    }
}
