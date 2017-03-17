<?php

class CreateBucketTest extends TestCase
{
    /**
     * Testing the user create bucket is failed.
     *
     * @return void
     */
    public function testCreateBucketFailed()
    {
        $user = $this->initUser();
        $token = \JWTAuth::fromUser($user);
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $this->post('/api/v1/bucket/create', ['bucket' => 'D'], $headers)
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "Create bucket is failed"
            ]);
    }

    /**
     * Testing the user create bucket but the name is invaid.
     *
     * @return void
     */
    public function testCreateBucketButNameIsInvaid()
    {
        $user = $this->initUser();
        $token = \JWTAuth::fromUser($user);
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $this->post('/api/v1/bucket/create', ['bucket' => '1'], $headers)
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The bucket name is invalid"
            ]);
    }

    /**
     * Testing the user create bucket but the bucket name is exist.
     *
     * @return void
     */
    public function testCreateBucketButNameIsExist()
    {
        $user = $this->initUser();
        $token = \JWTAuth::fromUser($user);
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $bucket = [
            'bucket' => str_random(10)
        ];
        $this->post('/api/v1/bucket/create', $bucket, $headers);
        $this->post('/api/v1/bucket/create', $bucket, $headers)
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The bucket name is exist"
            ]);
    }

    /**
     * Testing the user create bucket is successfully.
     *
     * @return void
     */
    public function testCreateBucketSuccess()
    {
        $user = $this->initUser();
        $token = \JWTAuth::fromUser($user);
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $this->post('/api/v1/bucket/create', ['bucket' => str_random(10)], $headers)
            ->seeStatusCode(200)
            ->seeJsonStructure(['Buckets' => ['*' => ['Name', 'CreationDate']]]);
    }
}
