<?php

class ListFileTest extends TestCase
{
    /**
     * Testing the user list file but the bucket is not exist.
     *
     * @return void
     */
    public function testListFileButBucketIsNotExist()
    {
        $user = $this->initUser();
        $token = \JWTAuth::fromUser($user);
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $this->get("api/v1/file/list/{str_random(10)}", $headers)
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The bucket is not exist"
            ]);
    }

    /**
     * Testing the user list file is successfully.
     *
     * @return void
     */
    public function testListFileSuccess()
    {
        $user = $this->initUser();
        $token = \JWTAuth::fromUser($user);
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $bucket = str_random(10);
        $this->createBucket($user, $bucket);
        $this->get("api/v1/file/list/{$bucket}", $headers)
            ->seeStatusCode(200)
            ->seeJsonStructure(['files']);
    }
}
