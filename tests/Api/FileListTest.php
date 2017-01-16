<?php

class FileListTest extends TestCase
{
    /**
     * The base Headers to use while testing the AuthLoginTest Class.
     *
     * @var array
     */
    protected $headers = [
        'HTTP_Accept' => 'application/json'
    ];
    /**
     * The base PostData to use while testing the AuthLoginTest Class.
     *
     * @var array
     */
    protected $postData = [
        'email' => 'User@imac.com',
        'password' => '123456'
    ];

    public function createBucket($user, $bucketName)
    {
        $s3Service = new \App\Services\BucketService($user['access_key'], $user['secret_key']);
        $s3Service->createBucket($bucketName);
    }
    public function testFileListFail()
    {
        $bucketName = str_random(10);
        $user = $this->createUser($this->postData['email'], $this->postData['password'], true);
        $token = \JWTAuth::fromUser($user);
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $response = $this->get("api/v1/file/list/{$bucketName}", $headers)
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "Bucket Error"
            ]);
    }
    public function testFileListSuccess()
    {
        $bucketName = str_random(10);
        $user = $this->createUser($this->postData['email'], $this->postData['password'], true);
        $token = \JWTAuth::fromUser($user);
        $this->createBucket($user, $bucketName);
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $response = $this->get("api/v1/file/list/{$bucketName}", $headers)->seeStatusCode(200);
    }
}
