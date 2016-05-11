<?php

class FileListTest extends TestCase
{
    /**
     * The base Headers to use while testing the AuthLoginTest Class.
     *
     * @var array
     */
    protected $headers = [
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

    public function createBucket($user, $bucketName)
    {
        $s3Service = new  \App\Services\S3Service;
        $s3Service->createBucket($user['access_key'], $user['secret_key'], $bucketName);
    }

    public function testFileListSuccess()
    {
        $bucketName = str_random(10);
        $user = $this->createUser($this->postData['email'], $this->postData['password'], true);
        $token = \JWTAuth::fromUser($user);

        $this->createBucket($user, $bucketName);

        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $response = $this->get("api/v1/file/list/{$bucketName}?", $headers)->seeStatusCode(200);
        $response
            ->seeJsonStructure([
                'files' => [
                    '*' => [
                        'Key',
                        'LastModified',
                        'ETag',
                        'Size',
                        'StorageClass',
                        'Owner' =>
                            ['*' =>
                                ['DisplayName', 'ID']
                            ]
                    ]
                ]
            ]);


    }
}