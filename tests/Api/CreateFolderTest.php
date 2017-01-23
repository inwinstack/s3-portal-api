<?php

use Symfony\Component\HttpFoundation\File\UploadedFile;

class CreateFolderTest extends TestCase
{
    /**
     * Testing the user create folder is successfully.
     *
     * @return void
     */
    public function testCreateFolderSuccess()
    {
        $init = $this->initBucket();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $bucketName = $init['bucketName'];
        $bucket = [
            'bucket' => $bucketName,
            'prefix' => str_random(10)
        ];
        $this->post('/api/v1/folder/create', $bucket, $headers)
            ->seeStatusCode(200)
            ->seeJsonContains([
                "message" => "Create Folder Success"
            ]);
    }

    /**
     * Testing the user create folder is failed.
     *
     * @return void
     */
    public function testCreateFolderFailed()
    {
        $init = $this->initBucket();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $bucketName = str_random(15);
        $userData = [
            'bucket' => $bucketName,
            'prefix' => str_random(15)
        ];
        $this->post('/api/v1/folder/create', $userData, $headers)
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "Bucket not Exist"
            ]);
    }
}
