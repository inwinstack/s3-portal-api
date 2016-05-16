<?php
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploadTest extends TestCase
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

    public function createBucket($user, $bucketName)
    {
        $s3Service = new  \App\Services\S3Service;
        $s3Service->createBucket($user['access_key'], $user['secret_key'], $bucketName);
    }

    public function initBucketAndGetToken()
    {
        $bucketName = str_random(10);
        $user = $this->createUser($this->postData['email'], $this->postData['password'], true);
        $token = \JWTAuth::fromUser($user);
        $this->createBucket($user, $bucketName);
        return ['bucketName' => $bucketName, 'token' => $token];
    }

    public function testFileUploadFailed()
    {
        $init = $this->initBucketAndGetToken();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $postData = [
            'bucket' => $init['bucketName']
        ];
        $this->post("/api/v1/file/create", $postData, $headers)
            ->seeStatusCode(422)
            ->seeJson(['message' => 'validator_error']);

    }

    public function testFileUploadSuccess()
    {
        $init = $this->initBucketAndGetToken();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";

        $local_file = __DIR__ . '/../test-files/test.jpg';
        $uploadedFile = new UploadedFile($local_file, 'test.jpg', 'image/jpeg', filesize($local_file), true);
        $postData = [
            'bucket' => $init['bucketName']
        ];
        $response = $this->call('post', 'api/v1/file/create', $postData, [], ['file' => $uploadedFile], $headers);
        $this->assertEquals($response->getStatusCode(), 200);
    }
}