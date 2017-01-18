<?php

use Symfony\Component\HttpFoundation\File\UploadedFile;

class RenameFileTest extends TestCase
{
    /**
     * Testing the user check the file is not exist.
     *
     * @return void
     */
    public function testFileNotExist()
    {
        $init = $this->initBucket();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $userData = [
            'bucket' => $init['bucketName'],
            'old' => str_random(10)
        ];
        $this->post('/api/v1/file/rename', $userData, $headers)->seeStatusCode(403)->seeJsonContains([
            "message" => "File Non-exist"
        ]);
    }

    /**
     * Testing the user check the file name is not used.
     *
     * @return void
     */
    public function testFileNameIsExist()
    {
        $init = $this->initBucket();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $bucketName = $init['bucketName'];
        $local_file = __DIR__ . '/../test-files/test.jpg';
        $uploadedFile = new UploadedFile($local_file, 'test.jpg', 'image/jpeg', filesize($local_file), true);
        $userData = [
            'bucket' => $init['bucketName'],
            'old' => 'test.jpg',
            'new' => 'test.jpg'
        ];
        $response = $this->call('post', 'api/v1/file/create', $userData, [], ['file' => $uploadedFile], $headers);
        $this->seeStatusCode(200);
        $s3Service = new  \App\Services\FileService($init['user']['access_key'], $init['user']['secret_key']);
        $this->post('/api/v1/file/rename', $userData, $headers)->seeStatusCode(403)->seeJsonContains([
            "message" => "File name has exist"
        ]);
    }

    /**
     * Testing the user rename file is successfully.
     *
     * @return void
     */
    public function testFileRenameSuccess()
    {
        $init = $this->initBucket();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $local_file = __DIR__ . '/../test-files/test.jpg';
        $uploadedFile = new UploadedFile($local_file, 'test.jpg', 'image/jpeg', filesize($local_file), true);
        $userData = [
            'bucket' => $init['bucketName'],
            'old' => 'test.jpg',
            'new' => str_random(10)
        ];
        $response = $this->call('post', 'api/v1/file/create', $userData, [], ['file' => $uploadedFile], $headers);
        $this->seeStatusCode(200);
        $s3Service = new  \App\Services\FileService($init['user']['access_key'], $init['user']['secret_key']);
        $this->post('/api/v1/file/rename', $userData, $headers)->seeStatusCode(200)->seeJsonContains([
            "message" => "Rename File Success"
        ]);
    }
}
