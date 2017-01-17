<?php

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploadTest extends TestCase
{
    /**
     * Testing the user upload file is successfully.
     *
     * @return void
     */
    public function testFileUploadSuccess()
    {
        $init = $this->initBucket();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $local_file = __DIR__ . '/../test-files/test.jpg';
        $uploadedFile = new UploadedFile($local_file, 'test.jpg', 'image/jpeg', filesize($local_file), true);
        $userData = [
            'bucket' => $init['bucketName']
        ];
        $response = $this->call('post', 'api/v1/file/create', $userData, [], ['file' => $uploadedFile], $headers);
        $this->assertEquals($response->getStatusCode(), 200);
    }

    /**
     * Testing the user upload file is failed.
     *
     * @return void
     */
    public function testFileUploadFailed()
    {
        $init = $this->initBucket();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $userData = [
            'bucket' => $init['bucketName']
        ];
        $this->post("/api/v1/file/create", $userData, $headers)
            ->seeStatusCode(422)
            ->seeJson(['message' => 'validator_error']);
    }
}
