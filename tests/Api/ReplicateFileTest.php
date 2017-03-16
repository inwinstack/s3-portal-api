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
        $user = $this->initUser();
        $token = \JWTAuth::fromUser($user);
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $this->post("api/v1/file/replicate/", [
              'bucket' => str_random(10),
              'file' => str_random(10)
            ], $headers)
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
        $user = $this->initUser();
        $token = \JWTAuth::fromUser($user);
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $bucket = str_random(10);
        $this->createBucket($user, $bucket);
        $this->post("api/v1/file/replicate/", [
              'bucket' => $bucket,
              'file' => str_random(10)
            ], $headers)
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
        $user = $this->initUser();
        $token = \JWTAuth::fromUser($user);
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $bucket = str_random(10);
        $this->createBucket($user, $bucket);
        $this->uploadFile($bucket, $headers);
        $this->post("api/v1/file/replicate/", [
              'bucket' => $bucket,
              'file' => 'test.jpg'
            ], $headers);
        $this->post("api/v1/file/replicate/", [
              'bucket' => $bucket,
              'file' => 'test.jpg'
            ], $headers)
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
        $user = $this->initUser();
        $token = \JWTAuth::fromUser($user);
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $bucket = str_random(10);
        $this->createBucket($user, $bucket);
        $this->uploadFile($bucket, $headers);
        $this->post("api/v1/file/replicate/", [
              'bucket' => $bucket,
              'file' => 'test.jpg'
            ], $headers)
            ->seeStatusCode(200)
            ->seeJsonContains([
                "message" => "Replication is successfully"
            ]);
    }
}
