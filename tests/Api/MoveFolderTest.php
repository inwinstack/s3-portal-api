<?php

class MoveFolderTest extends TestCase
{
    /**
     * Testing the user move folder but the bucket of source is not exist.
     *
     * @return void
     */
    public function testMoveFolderButSourceBucketIsNotExist()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $this->post("/api/v1/folder/move?token={$token}", [
            "sourceBucket" => str_random(10),
            "sourceFolder" => str_random(10),
            "goalBucket" => str_random(10),
            "goalFolder" => str_random(10)
        ], [])
        ->seeStatusCode(403)
        ->seeJsonContains([
            "message" => "The bucket of source is not exist"
        ]);
    }

    /**
     * Testing the user move folder but the bucket of goal is not exist.
     *
     * @return void
     */
    public function testMoveFolderButGoalBucketIsNotExist()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $bucket = str_random(10);
        $this->createBucket($user, $bucket);
        $this->post("/api/v1/folder/move?token={$token}", [
            "sourceBucket" => $bucket,
            "sourceFolder" => str_random(10),
            "goalBucket" => str_random(10),
            "goalFolder" => str_random(10)
        ], [])
        ->seeStatusCode(403)
        ->seeJsonContains([
            "message" => "The bucket of goal is not exist"
        ]);
    }

    /**
     * Testing the user move folder but the folder of source is not exist.
     *
     * @return void
     */
    public function testMoveFolderButSourceFolderIsNotExist()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $bucket = str_random(10);
        $this->createBucket($user, $bucket);
        $this->post("/api/v1/folder/move?token={$token}", [
            "sourceBucket" => $bucket,
            "sourceFolder" => str_random(10),
            "goalBucket" => $bucket,
            "goalFolder" => str_random(10)
        ], [])
        ->seeStatusCode(403)
        ->seeJsonContains([
            "message" => "The folder of source is not exist"
        ]);
    }

    /**
     * Testing the user move folder but the folder of goal is exist.
     *
     * @return void
     */
    public function testMoveFolderButSourceFolderIsExist()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $bucket = str_random(10);
        $folder = str_random(10);
        $this->createBucket($user, $bucket);
        $this->createFolder($user, $bucket, $folder);
        $this->post("/api/v1/folder/move?token={$token}", [
            "sourceBucket" => $bucket,
            "sourceFolder" => $folder,
            "goalBucket" => $bucket,
            "goalFolder" => $folder
        ], [])
        ->seeStatusCode(403)
        ->seeJsonContains([
            "message" => "The folder of goal is exist"
        ]);
    }

    /**
     * Testing the user move folder is successfully.
     *
     * @return void
     */
    public function testMoveFolderSuccess()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $bucket = str_random(10);
        $folder = str_random(10);
        $this->createBucket($user, $bucket);
        $this->createFolder($user, $bucket, $folder);
        $this->post("/api/v1/folder/move?token={$token}", [
            "sourceBucket" => $bucket,
            "sourceFolder" => $folder,
            "goalBucket" => $bucket,
            "goalFolder" => str_random(10)
        ], [])
        ->seeStatusCode(200)
        ->seeJsonContains([
            "message" => "The move is complete"
        ]);
    }
}
