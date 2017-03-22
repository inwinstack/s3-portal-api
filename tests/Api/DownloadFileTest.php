<?php

class DownloadFileTest extends TestCase
{
    /**
     * Testing the user download file is failed.
     *
     * @return void
     */
    public function testDownloadFilefailed()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $bucket = str_random(10);
        $this->createBucket($user, $bucket);
        $this->uploadFile($bucket, $token);
        $this->get("/api/v1/file/get/{$bucket}/test1.jpg?token={$token}", [])
            ->seeStatusCode(403)
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
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $bucket = str_random(10);
        $this->createBucket($user, $bucket);
        $this->uploadFile($bucket, $token);
        $this->get("/api/v1/file/get/{$bucket}/test.jpg?token={$token}", [])
            ->seeStatusCode(200);
    }
}
