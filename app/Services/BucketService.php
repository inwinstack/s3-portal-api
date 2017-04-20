<?php

namespace App\Services;

use Aws\S3\S3Client;
use Aws\S3\Model\ClearBucket;
use Aws\S3\Exception\S3Exception as S3Exception;

class BucketService extends S3Service
{
    protected $s3;

    public function __construct($accessKey, $secretKey)
    {
        $this->s3 = $this->connect($accessKey, $secretKey);
    }

    public function get()
    {
        try {
            return $this->s3->listBuckets([]);
        } catch (S3Exception $e) {
            return false;
        }
    }

    public function create($Bucket)
    {
        try {
            $this->s3->createBucket([
                'Bucket' => $Bucket,
            ]);
            return true;
        } catch (S3Exception $e) {
            return false;
        }
    }

    public function cors($bucket)
    {
        try {
            $this->s3->putBucketCors([
                'Bucket' => $bucket,
                'CORSRules' => array(array(
                    'AllowedHeaders' => array('*'),
                    'AllowedMethods' => array('HEAD', 'GET', 'PUT', 'POST', 'DELETE'),
                    'AllowedOrigins' => array('*'),
                    'ExposeHeaders' => array('ETag')
                ))
            ]);
            return true;
        } catch (S3Exception $e) {
            return false;
        }
    }

    public function exist($bucket)
    {
        try {
            return $this->s3->doesBucketExist($bucket);
        } catch (S3Exception $e) {
            return true;
        }
    }

    public function check($bucket)
    {
        try {
            $this->s3->headBucket(['Bucket' => $bucket]);
            return true;
        } catch (S3Exception $e) {
            return false;
        }
    }

    public function delete($bucket)
    {
        $clear = new ClearBucket($this->s3, $bucket);
        $clear->clear();
        try {
            $bucketResponse = $this->s3->deleteBucket([
                'Bucket' => $bucket
            ]);
            return true;
        } catch (S3Exception $e) {
            return false;
        }
    }
}
