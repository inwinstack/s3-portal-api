<?php

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileDownloadTest extends TestCase
{
    /**
     * Testing the user download file is successfully.
     *
     * @return void
     */
    public function testFileDownloadSuccess()
    {
        $init = $this->initBucket();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $bucketName = $init['bucketName'];
        $local_file = __DIR__ . '/../test-files/test-download.jpg';
        $uploadedFile = new UploadedFile($local_file, 'test-download.jpg', 'image/jpeg', filesize($local_file), true);
        $bucketData = [
            'bucket' => $init['bucketName']
        ];
        $response = $this->call('post', 'api/v1/file/create', $bucketData, [], ['file' => $uploadedFile], $headers);
        $this->seeStatusCode(200);
        $s3Service = new \App\Services\FileService($init['user']['access_key'], $init['user']['secret_key']);
        $fileList = $s3Service->listFile($bucketName, '');
        $this->get("/api/v1/file/get/" . $init['bucketName'] . "/test-download", $headers);
    }
}
