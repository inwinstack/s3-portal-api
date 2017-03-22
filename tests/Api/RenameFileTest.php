<?php

class RenameFileTest extends TestCase
{
    /**
     * Testing the user rename the file but the bucket is not exist.
     *
     * @return void
     */
    public function testRenameFileButBucketIsNotExist()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $this->post("api/v1/file/rename?token={$token}", [
            "bucket" => str_random(10),
            "old" => "old",
            "new" => "new"
        ], [])
        ->seeStatusCode(403)
        ->seeJsonContains([
            "message" => "The bucket is not exist"
        ]);
    }

    /**
     * Testing the user rename the file but the file of old is not exist.
     *
     * @return void
     */
    public function testRenameFileButOldFileIsNotExist()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $bucket = str_random(10);
        $this->createBucket($user, $bucket);
        $this->post("api/v1/file/rename?token={$token}", [
            "bucket" => $bucket,
            "old" => "old",
            "new" => "new"
        ], [])
        ->seeStatusCode(403)
        ->seeJsonContains([
            "message" => "The file of old name is not exist"
        ]);
    }

    /**
     * Testing the user rename the file but the file of new is exist.
     *
     * @return void
     */
    public function testRenameFileButNewFileIsExist()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $bucket = str_random(10);
        $this->createBucket($user, $bucket);
        $this->uploadFile($bucket, $token);
        $this->post("api/v1/file/rename?token={$token}", [
            "bucket" => $bucket,
            "old" => "test.jpg",
            "new" => "test.jpg"
        ], [])
        ->seeStatusCode(403)
        ->seeJsonContains([
            "message" => "The file of new name is exist"
        ]);
    }

    /**
     * Testing the user rename the file is successfully.
     *
     * @return void
     */
    public function testRenameFileSuccess()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $bucket = str_random(10);
        $this->createBucket($user, $bucket);
        $this->uploadFile($bucket, $token);
        $this->post("api/v1/file/rename?token={$token}", [
            "bucket" => $bucket,
            "old" => "test.jpg",
            "new" => "test2.jpg"
        ], [])
        ->seeStatusCode(200)
        ->seeJsonContains([
            "message" => "Rename file is Successfully"
        ]);
    }
}
