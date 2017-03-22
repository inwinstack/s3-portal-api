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
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $this->get("api/v1/file/list/{str_random(10)}?token={$token}", [])
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
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $bucket = str_random(10);
        $this->createBucket($user, $bucket);
        $this->get("api/v1/file/list/{$bucket}?token={$token}", [])
            ->seeStatusCode(200)
            ->seeJsonStructure(["files"]);
    }
}
