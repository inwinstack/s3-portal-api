<?php

namespace App\Services;

use Aws\S3\S3Client;
class S3Service
{
    public function connect($accessKey, $secretKey)
    {
        $s3 = S3Client::factory([
            'credentials' => [
                'key'    => $accessKey,
                'secret' => $secretKey,
            ],
            'endpoint' => 'http://ceph-s3.imaclouds.com/',

        ]);
        return $s3;
    }

    public function listBucket($accessKey, $secretKey)
    {
        $s3 = $this->connect($accessKey, $secretKey);
        $listResponse = $s3->listBuckets([]);
        return $listResponse;
    }

    public function createBucket($accessKey, $secretKey, $Bucket)
    {
        $s3 = $this->connect($accessKey, $secretKey);
        try {
            $bucketResponse = $s3->createBucket([
                'Bucket' => $Bucket,
            ]);
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }

    }
}
