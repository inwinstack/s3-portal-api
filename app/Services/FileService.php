<?php

namespace App\Services;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception as S3Exception;

class FileService extends S3Service
{
    protected $s3;

    public function __construct($accessKey, $secretKey)
    {
        $this->s3 = $this->connect($accessKey, $secretKey);
    }

    public function get($bucket, $prefix)
    {
        try {
            $objects = $this->s3->listObjects([
                'Bucket' => $bucket,
                'Prefix' => $prefix,
            ]);
            return $objects;
        } catch (S3Exception $e) {
            return false;
        }
    }

    public function upload($bucket, $file, $fileName, $prefix)
    {
        try {
            $result = $this->s3->putObject([
                'Bucket'     => $bucket,
                'Key'        => "$prefix$fileName",
                'SourceFile' => $file,
            ]);
            return true;
        } catch (S3Exception $e) {
            return false;
        }
    }

    public function download($bucket, $key)
    {
        $randomString = sha1($key . str_random(32));
        try {
            $result = $this->s3->getObject([
                'Bucket' => $bucket,
                'Key' => $key,
                'SaveAs' => storage_path('tmpfile/' . $randomString)
            ]);
            return $randomString;
        } catch (S3Exception $e) {
            return false;
        }
    }

    public function delete($bucket, $key)
    {
        try {
            $this->s3->deleteObject([
                'Bucket' => $bucket,
                'Key' => $key
            ]);
            return true;
        } catch (S3Exception $e) {
            return false;
        }
    }

    public function check($bucket, $file)
    {
        try {
            return $this->s3->doesObjectExist($bucket, $file);
        } catch (S3Exception $e) {
            return false;
        }
    }

    public function rename($bucket, $old, $new)
    {
        try {
            $this->s3->copyObject([
                'Bucket' => $bucket,
                'CopySource' => $bucket . '/' . $old,
                'Key' => $new
            ]);
            $this->s3->deleteObject([
                'Bucket' => $bucket,
                'Key' => $old
            ]);
            return true;
        } catch (S3Exception $e) {
            return false;
        }
    }

    public function move($sourceBucket, $sourceFile, $goalBucket, $goalFile)
    {
        try {
            $this->s3->copyObject([
                'Bucket' => $goalBucket,
                'CopySource' => $sourceBucket . '/' . $sourceFile,
                'Key' => $goalFile
            ]);
            $this->s3->deleteObject([
                'Bucket' => $sourceBucket,
                'Key' => $sourceFile
            ]);
            return true;
        } catch (S3Exception $e) {
            return false;
        }
    }

    public function replicate($bucket, $file)
    {
        try {
            $this->s3->copyObject([
                'Bucket' => $bucket,
                'CopySource' => $bucket . '/' . $file,
                'Key' => pathinfo($file, PATHINFO_FILENAME) . '_copy.' . pathinfo($file, PATHINFO_EXTENSION)
            ]);
            return true;
        } catch (S3Exception $e) {
            return false;
        }
    }
}
