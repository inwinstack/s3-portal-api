<?php

class MoveFileTest extends TestCase
{
    /**
     * Testing the user move the file but the bucket of source is not exist.
     *
     * @return void
     */
    public function testMoveFileButSourceBucketIsNotExist()
    {
        $user = $this->initUser();
        $token = \JWTAuth::fromUser($user);
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $this->post("api/v1/file/move/", [
              'sourceBucket' => str_random(10),
              'goalBucket' => str_random(10),
              'sourceFile' => 'source',
              'goalFile' => 'goal'
            ], $headers)
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The bucket of source is not exist"
            ]);
    }

    /**
     * Testing the user move the file but the bucket of goal is not exist.
     *
     * @return void
     */
    public function testMoveFileButGoalBucketIsNotExist()
    {
        $user = $this->initUser();
        $token = \JWTAuth::fromUser($user);
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $bucket = str_random(10);
        $this->createBucket($user, $bucket);
        $this->post("api/v1/file/move/", [
              'sourceBucket' => $bucket,
              'goalBucket' => str_random(10),
              'sourceFile' => 'source',
              'goalFile' => 'goal'
            ], $headers)
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The bucket of goal is not exist"
            ]);
    }

    /**
     * Testing the user move the file but the file of source is not exist in source bucket.
     *
     * @return void
     */
    public function testMoveFileButSourceFileIsNotExist()
    {
        $user = $this->initUser();
        $token = \JWTAuth::fromUser($user);
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $bucket = str_random(10);
        $this->createBucket($user, $bucket);
        $this->post("api/v1/file/move/", [
              'sourceBucket' => $bucket,
              'goalBucket' => $bucket,
              'sourceFile' => 'source',
              'goalFile' => 'goal'
            ], $headers)
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The file of source is not exist in source bucket"
            ]);
    }

    /**
     * Testing the user move the file but the file of goal is exist in goal bucket.
     *
     * @return void
     */
    public function testMoveFileButGoalFileIsExist()
    {
        $user = $this->initUser();
        $token = \JWTAuth::fromUser($user);
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $bucket = str_random(10);
        $this->createBucket($user, $bucket);
        $this->uploadFile($bucket, $headers);
        $this->post("api/v1/file/move/", [
              'sourceBucket' => $bucket,
              'goalBucket' => $bucket,
              'sourceFile' => 'test.jpg',
              'goalFile' => 'test.jpg'
            ], $headers)
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The file of goal is exist in goal bucket"
            ]);
    }

    /**
     * Testing the user move the file is successfully.
     *
     * @return void
     */
    public function testMoveFileSuccess()
    {
        $user = $this->initUser();
        $token = \JWTAuth::fromUser($user);
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $bucket = str_random(10);
        $this->createBucket($user, $bucket);
        $this->uploadFile($bucket, $headers);
        $this->post("api/v1/file/move/", [
              'sourceBucket' => $bucket,
              'goalBucket' => $bucket,
              'sourceFile' => 'test.jpg',
              'goalFile' => 'test2.jpg'
            ], $headers)
            ->seeStatusCode(200)
            ->seeJsonContains([
                "message" => "Move file is successfully"
            ]);
    }
}
