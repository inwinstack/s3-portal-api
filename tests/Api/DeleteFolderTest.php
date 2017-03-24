<?php

class DeleteFolderTest extends TestCase
{
    /**
     * Testing the user delete folder but the bucket is not exist.
     *
     * @return void
     */
    public function testDeleteFolderButBucketIsNotExist()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $this->delete("/api/v1/folder/delete/{str_random(10)}/{str_random(10)}?token={$token}", [], [])
        ->seeStatusCode(403)
        ->seeJsonContains([
            "message" => "The bucket is not exist"
        ]);
    }

    /**
     * Testing the user delete folder but the folder is not exist.
     *
     * @return void
     */
    public function testDeleteFolderButFolderIsNotExist()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $bucket = str_random(10);
        $this->createBucket($user, $bucket);
        $this->delete("/api/v1/folder/delete/{$bucket}/{str_random(10)}?token={$token}", [], [])
        ->seeStatusCode(403)
        ->seeJsonContains([
            "message" => "The folder is not exist"
        ]);
    }

    /**
     * Testing the user delete folder is successfully.
     *
     * @return void
     */
    public function testDeleteFolderSuccess()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $bucket = str_random(10);
        $folder = str_random(10);
        $this->createBucket($user, $bucket);
        $this->createFolder($user, $bucket, $folder);
        $this->delete("/api/v1/folder/delete/{$bucket}/{$folder}?token={$token}", [], [])
        ->seeStatusCode(200)
        ->seeJsonContains([
            "message" => "Delete folder is successfully"
        ]);
    }
}
