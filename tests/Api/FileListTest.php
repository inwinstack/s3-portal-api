<?php

class FileListTest extends TestCase
{
    /**
     * Testing the user watch file list is successfully.
     *
     * @return void
    */
    public function testFileListSuccess()
    {
        $bucketName = str_random(10);
        $user = $this->createUser($this->userData['email'], $this->userData['password'], true);
        $token = \JWTAuth::fromUser($user);
        $this->createBucket($user, $bucketName);
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $response = $this->get("api/v1/file/list/{$bucketName}", $headers)->seeStatusCode(200);
    }

    /**
     * Testing the user watch file list is failed.
     *
     * @return void
     */
    public function testFileListFail()
    {
        $bucketName = str_random(10);
        $user = $this->createUser($this->userData['email'], $this->userData['password'], true);
        $token = \JWTAuth::fromUser($user);
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $response = $this->get("api/v1/file/list/{$bucketName}", $headers)
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "Bucket Error"
            ]);
    }
}
