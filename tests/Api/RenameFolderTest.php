<?php

class RenameFolderTest extends TestCase
{
    /**
     * Testing the user rename folder but the bucket is not exist.
     *
     * @return void
     */
    public function testRenameFolderButBucketIsNotExist()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $this->post("/api/v1/folder/rename?token={$token}", [
            "bucket" => str_random(10),
            "oldName" => str_random(10),
            "newName"=> str_random(10)
        ], [])
        ->seeStatusCode(403)
        ->seeJsonContains([
            "message" => "The bucket is not exist"
        ]);
    }

    /**
     * Testing the user rename folder but the folder of old name is not exist.
     *
     * @return void
     */
    public function testRenameFolderButOldNameFolderIsNotExist()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $bucket = str_random(10);
        $this->createBucket($user, $bucket);
        $this->post("/api/v1/folder/rename?token={$token}", [
            "bucket" => $bucket,
            "oldName" => str_random(10),
            "newName"=> str_random(10)
        ], [])
        ->seeStatusCode(403)
        ->seeJsonContains([
            "message" => "The old name is not exist"
        ]);
    }

    /**
     * Testing the user rename folder but the folder of new name is exist.
     *
     * @return void
     */
    public function testRenameFolderButNewNameFolderIsExist()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $bucket = str_random(10);
        $folder = str_random(10);
        $this->createBucket($user, $bucket);
        $this->createFolder($user, $bucket, $folder);
        $this->post("/api/v1/folder/rename?token={$token}", [
            "bucket" => $bucket,
            "oldName" => $folder,
            "newName"=> $folder
        ], [])
        ->seeStatusCode(403)
        ->seeJsonContains([
            "message" => "The new name is exist"
        ]);
    }

    /**
     * Testing the user rename folder is successfully.
     *
     * @return void
     */
    public function testRenameFolderSuccess()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $bucket = str_random(10);
        $folder = str_random(10);
        $this->createBucket($user, $bucket);
        $this->createFolder($user, $bucket, $folder);
        $this->post("/api/v1/folder/rename?token={$token}", [
            "bucket" => $bucket,
            "oldName" => $folder,
            "newName"=> str_random(10)
        ], [])
        ->seeStatusCode(200)
        ->seeJsonContains([
            "message" => "The renamed is successfully"
        ]);
    }
}
