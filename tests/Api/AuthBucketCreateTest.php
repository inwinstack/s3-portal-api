<?php

class AuthBucketCreateTest extends TestCase
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
        'email' => 'ApiTestEmail@yahoo.com.tw',
        'password' => 'ApiTestPassword'
    ];
    public function createBucket($user, $bucketName)
    {
        $s3Service = new  \App\Services\BucketService($user['access_key'], $user['secret_key']);
        $s3Service->createBucket($bucketName);
    }
    public function initBucketAndGetToken()
    {
        $bucketName = str_random(10);
        $user = $this->createUser($this->postData['email'], $this->postData['password'], true);
        $token = \JWTAuth::fromUser($user);
        $this->createBucket($user, $bucketName);
        return ['bucketName' => $bucketName, 'token' => $token, 'user' => $user];
    }
    public function testBucketCreateSuccess()
    {
        $init = $this->initBucketAndGetToken();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $bucket = [
            'bucket' => str_random(14)
        ];
        $response = $this->post('/api/v1/bucket/create', $bucket, $headers)
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'Buckets' => [
                    '*' => ['Name', 'CreationDate']
                ]
            ])
            ->response->content();
        $response = json_decode($response, true);
        $hasName = false;
        foreach ($response['Buckets'] as $value) {
            if ($value['Name'] == $bucket['bucket']) {
                $hasName = true;
            }
        }
        $this->assertTrue($hasName);
    }
    public function testCheckBucketIsExist()
    {
        $init = $this->initBucketAndGetToken();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $bucket = [
            'bucket' => str_random(10)
        ];
        $this->post('/api/v1/bucket/create', $bucket, $headers);
        $this->post('/api/v1/bucket/create', $bucket, $headers)
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "Has Bucket"
            ]);
    }
    public function testCreateBucketFail()
    {
        $init = $this->initBucketAndGetToken();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $bucket = [
            'bucket' => str_random(1)
        ];
        $this->post('/api/v1/bucket/create',$bucket, $headers)
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "Create Bucket Error"
            ]);
    }
}