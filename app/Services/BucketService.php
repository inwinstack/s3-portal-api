<?php

namespace App\Services;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception as S3Exception;
class BucketService extends S3Service
{
    protected $s3;

    public function __construct($accessKey, $secretKey)
    {
        $this->s3 = $this->connect($accessKey, $secretKey);
    }

    public function listBucket()
    {
        $listResponse = $this->s3->listBuckets([]);
        return $listResponse;
    }

    public function createBucket($Bucket)
    {
        try {
            $bucketResponse = $this->s3->createBucket([
                'Bucket' => $Bucket,
            ]);
            return true;
        } catch (S3Exception $e) {
            return false;
        }
    }

    public function checkBucket($bucket)
    {
        return $this->s3->doesBucketExist($bucket);
    }
}
