<?php

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileDownloadTest extends TestCase
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

    public function testFileDownloadSuccess()
    {
        $init = $this->initBucketAndGetToken();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $bucketName = $init['bucketName'];

        $local_file = __DIR__ . '/../test-files/test-download.jpg';
        $uploadedFile = new UploadedFile($local_file, 'test-download.jpg', 'image/jpeg', filesize($local_file), true);
        $postData = [
            'bucket' => $init['bucketName']
        ];
        $response = $this->call('post', 'api/v1/file/create', $postData, [], ['file' => $uploadedFile], $headers);
        $this->seeStatusCode(200);
        $s3Service = new  \App\Services\FileService($init['user']['access_key'], $init['user']['secret_key']);
        $fileList = $s3Service->listFile($bucketName, '');
        $this->get("/api/v1/file/get/" . $init['bucketName'] . "/test-download", $headers);

    }


}