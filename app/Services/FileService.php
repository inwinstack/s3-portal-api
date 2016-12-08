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
        $checkObject = $this->s3->doesObjectExist($bucket, $prefix);
        if ($checkObject) {
            return 'Folder exist';
        }
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

    public function renameFile($bucket, $old, $new)
    {
        $key = $old;
        $checkOldObject = $this->s3->doesObjectExist($bucket, $key);
        if (!$checkOldObject) {
            return 'File Non-exist';
        }
        $key = $new;
        $checkNewObject = $this->s3->doesObjectExist($bucket, $key);
        if ($checkNewObject) {
            return 'File name has exist';
        }
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
            return false;
        } catch (S3Exception $e) {
            return 'Rename File Error';
        }
    }

    public function moveFile($sourceBucket, $sourceFile, $goalBucket, $goalFile)
    {
        $checkSourceExist = $this->s3->doesObjectExist($sourceBucket, $sourceFile);
        if (!$checkSourceExist) return 'The file don\'t exist';
        $checkGoalExist = $this->s3->doesObjectExist($goalBucket, $goalFile);
        if ($checkGoalExist) return 'The file already exists';
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
            return false;
        } catch (S3Exception $e) {
            return 'The file move failed';
        }
    }

    public function replicateFile($bucket, $file)
    {
        $checkFileExist = $this->s3->doesObjectExist($bucket, $file);
        if (!$checkFileExist) return 'The file don\'t exist';
        try {
            $this->s3->copyObject([
                'Bucket' => $bucket,
                'CopySource' => $bucket . '/' . $file,
                'Key' => explode('.', $file)[0] . '(copy).' . explode('.', $file)[1]
            ]);
            return false;
        } catch (S3Exception $e) {
            return 'The file copy failed';
        }
    }
}
