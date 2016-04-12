<?php


class AuthBucketListTest extends TestCase
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

    public function testBucketListSuccess()
    {
        $token = \JWTAuth::fromUser($this->createUser($this->postData['email'], $this->postData['password'], true));

        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";

        $this->post('/api/v1/bucket/list', [], $headers)
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'Buckets' => [
                    '*' => ['Name', 'CreationDate']
                ]
            ]);
    }

}