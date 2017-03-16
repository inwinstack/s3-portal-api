<?php

use Symfony\Component\HttpFoundation\File\UploadedFile;

class DownloadFileTest extends TestCase
{
    /**
     * Testing the user download file is failed.
     *
     * @return void
     */
    public function testDownloadFilefailed()
    {
        $user = $this->initUser();
        $token = \JWTAuth::fromUser($user);
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $bucket = str_random(10);
        $this->createBucket($user, $bucket);
        $this->uploadFile($bucket, $headers);
        $this->get("/api/v1/file/get/" . $bucket . "/test1.jpg", $headers)
            ->seeStatusCode(403);
            ->seeJsonContains([
                "message" => "Download file is failed"
            ]);
    }

    /**
     * Testing the user download file is successfully.
     *
     * @return void
     */
    public function testDownloadFileSuccess()
    {
        $user = $this->initUser();
        $token = \JWTAuth::fromUser($user);
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $bucket = str_random(10);
        $this->createBucket($user, $bucket);
        $this->uploadFile($bucket, $headers);
        $this->get("/api/v1/file/get/" . $bucket . "/test.jpg", $headers)
            ->seeStatusCode(200);
    }
}
