<?php

class ReplicateFileTest extends TestCase
{
    /**
     * Testing the user replicate the file but the bucket is not exist.
     *
     * @return void
     */
    public function testReplicateFileButBucketIsNotExist()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $this->post("api/v1/file/replicate?token={$token}", [
            "bucket" => str_random(10),
            "file" => str_random(10)
        ], [])
        ->seeStatusCode(403)
        ->seeJsonContains([
            "message" => "The bucket is not exist"
        ]);
    }

    /**
     * Testing the user replicate the file but the file is not exist.
     *
     * @return void
     */
    public function testReplicateFileButFileIsNotExist()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $bucket = str_random(10);
        $this->createBucket($user, $bucket);
        $this->post("api/v1/file/replicate?token={$token}", [
            "bucket" => $bucket,
            "file" => str_random(10)
        ], [])
        ->seeStatusCode(403)
        ->seeJsonContains([
            "message" => "The file is not exist"
        ]);
    }

    /**
     * Testing the user replicate the file but the replicas is exist.
     *
     * @return void
     */
    public function testReplicateFileButReplicasIsExist()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $bucket = str_random(10);
        $this->createBucket($user, $bucket);
        $this->uploadFile($bucket, $token);
        $this->post("api/v1/file/replicate?token={$token}", [
            "bucket" => $bucket,
            "file" => "test.jpg"
        ], []);
        $this->post("api/v1/file/replicate?token={$token}", [
            "bucket" => $bucket,
            "file" => "test.jpg"
        ], [])
        ->seeStatusCode(403)
        ->seeJsonContains([
            "message" => "The replicas is exist"
        ]);
    }

    /**
     * Testing the user replicate is successfully.
     *
     * @return void
     */
    public function testReplicateFileSuccess()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $bucket = str_random(10);
        $this->createBucket($user, $bucket);
        $this->uploadFile($bucket, $token);
        $this->post("api/v1/file/replicate?token={$token}", [
            "bucket" => $bucket,
            "file" => "test.jpg"
        ], [])
        ->seeStatusCode(200)
        ->seeJsonContains([
            "message" => "Replication is successfully"
        ]);
    }
}
