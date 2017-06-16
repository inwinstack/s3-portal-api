<?php

namespace App\Services;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception as S3Exception;

class FolderService extends S3Service
{
    protected $s3;

    public function __construct($accessKey, $secretKey)
    {
        $this->s3 = $this->connect($accessKey, $secretKey);
    }

    public function store($bucket, $prefix)
    {
        try {
            $result = $this->s3->putObject([
                'Bucket'     => $bucket,
                'Key'        => $prefix . '/',
                'Body'       => "",
            ]);
            return true;
        } catch (S3Exception $e) {
            return false;
        }
    }

    public function delete($bucket, $key, $fileService)
    {
        $files = $fileService->get($bucket, $key . '/')->get('Contents');
        foreach ($files as $key => $value) {
            try {
                $this->s3->deleteObject([
                    'Bucket' => $bucket,
                    'Key' => $value['Key']
                ]);
            } catch (S3Exception $e) {
                return false;
            }
        }
        return true;
    }

    public function rename($bucket, $oldName, $newName, $fileService)
    {
        try {
            $this->s3->copyObject([
                'Bucket' => $bucket,
                'CopySource' => $bucket . '/' . $oldName . '/',
                'Key' => $newName . '/'
            ]);
            $files = $fileService->get($bucket, $oldName . '/')->get('Contents');
            foreach ($files as $key => $value) {
                $fileName = explode($oldName . '/', $value['Key'])[1];
                if ($key != 0) {
                    $this->s3->copyObject([
                        'Bucket' => $bucket,
                        'CopySource' => $bucket . '/' . $value['Key'],
                        'Key' => $newName . '/' . $fileName
                    ]);
                }
                $this->s3->deleteObject([
                    'Bucket' => $bucket,
                    'Key' => $oldName . '/'. $fileName
                ]);
            }
            return true;
        } catch (S3Exception $e) {
            return false;
        }
    }

    public function move($sourceBucket, $sourceFolder, $goalBucket, $goalFolder, $fileService)
    {
        try {
            $this->s3->copyObject([
                'Bucket' => $goalBucket,
                'CopySource' => $sourceBucket . '/' . $sourceFolder . '/',
                'Key' => $goalFolder . '/'
            ]);
            $files = $fileService->get($sourceBucket, $sourceFolder . '/')->get('Contents');
            foreach ($files as $key => $value) {
                $fileName = explode($sourceFolder . '/', $value['Key'])[1];
                $this->s3->copyObject([
                    'Bucket' => $goalBucket,
                    'CopySource' => $sourceBucket . '/' . $value['Key'],
                    'Key' => $goalFolder . '/' . $fileName
                ]);
                $this->s3->deleteObject([
                    'Bucket' => $sourceBucket,
                    'Key' => $sourceFolder . '/' . $fileName
                ]);
            }
            return true;
        } catch (S3Exception $e) {
            return false;
        }
    }

    public function check($bucket, $folderName)
    {
        try {
            return $this->s3->doesObjectExist($bucket, $folderName . '/');
        } catch (S3Exception $e) {
            return false;
        }
    }
}
