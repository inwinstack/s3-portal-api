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

    public function listFile($bucket, $prefix)
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

    public function uploadFile($bucket, $file, $fileName, $prefix)
    {
        $checkBucket = $this->checkHeadBucket($bucket);
        if (!$checkBucket) {
            try {
                $result = $this->s3->putObject([
                    'Bucket'     => $bucket,
                    'Key'        => "$prefix$fileName",
                    'SourceFile' => $file,
                ]);
                return false;
            } catch (S3Exception $e) {
                return 'Upload File Error';
            }
        }
        return $checkBucket;
    }

    public function getFile($bucket, $key)
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

    public function storeFolder($bucket, $prefix)
    {
        $checkBucket = $this->checkHeadBucket($bucket);
        if (!$checkBucket) {
            try {
                $result = $this->s3->putObject([
                    'Bucket'     => $bucket,
                    'Key'        => "$prefix",
                    'Body'       => "",
                ]);
                return false;
            } catch (S3Exception $e) {
                return 'Create Folder Error';
            }
        }
        return $checkBucket;

    }

    public function checkHeadBucket($bucket)
    {
        try {
            $this->s3->headBucket(['Bucket' => $bucket]);
            return false;
        } catch (S3Exception $e) {
            return 'Bucket not Exist';
        }
    }

    public function deleteFile($bucket, $key)
    {
        $checkObject = $this->s3->doesObjectExist($bucket, $key);
        if (!$checkObject) {
            return 'File Non-exist';
        }
        try {
            $this->s3->deleteObject([
                'Bucket' => $bucket,
                'Key' => $key
            ]);
            return false;
        } catch (S3Exception $e) {
            return 'Delete File Error';
        }
    }

    public function deleteFolder($bucket, $key)
    {
        $checkObject = $this->s3->doesObjectExist($bucket, $key . '/');
        if (!$checkObject) {
            return 'Folder Non-exist';
        }
        $files = $this->listFile($bucket, $key)->get('Contents');

        foreach ($files as $key => $value) {
            try {
                $this->s3->deleteObject([
                    'Bucket' => $bucket,
                    'Key' => $value['Key']
                ]);
            } catch (S3Exception $e) {
                return 'Delete Folder Error';
            }
        }
        return false;
    }
}
