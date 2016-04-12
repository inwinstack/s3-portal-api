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
        'email' => 'example@example.com',
        'password' => 'test1234'
    ];

    public function testBucketCreateSuccess()
    {
        $token = \JWTAuth::fromUser($this->createUser($this->postData['email'], $this->postData['password'], true));

        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";

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
    

}