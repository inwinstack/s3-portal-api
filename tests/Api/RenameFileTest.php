<?php

use Symfony\Component\HttpFoundation\File\UploadedFile;

class RenameFileTest extends TestCase
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
    public function testFileNotExist()
    {
        $init = $this->initBucketAndGetToken();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $postData = [
            'bucket' => $init['bucketName'],
            'old' => str_random(10)
        ];
        $this->post('/api/v1/file/rename', $postData, $headers)->seeStatusCode(403)->seeJsonContains([
            "message" => "File Non-exist"
        ]);
    }
    public function testFileNameIsExist()
    {
        $init = $this->initBucketAndGetToken();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $bucketName = $init['bucketName'];
        $local_file = __DIR__ . '/../test-files/test.jpg';
        $uploadedFile = new UploadedFile($local_file, 'test.jpg', 'image/jpeg', filesize($local_file), true);
        $postData = [
            'bucket' => $init['bucketName'],
            'old' => 'test.jpg',
            'new' => 'test.jpg'
        ];
        $response = $this->call('post', 'api/v1/file/create', $postData, [], ['file' => $uploadedFile], $headers);
        $this->seeStatusCode(200);
        $s3Service = new  \App\Services\FileService($init['user']['access_key'], $init['user']['secret_key']);
        $this->post('/api/v1/file/rename', $postData, $headers)->seeStatusCode(403)->seeJsonContains([
            "message" => "File name has exist"
        ]);
    }
    public function testFileRenameSuccess()
    {
        $init = $this->initBucketAndGetToken();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $local_file = __DIR__ . '/../test-files/test.jpg';
        $uploadedFile = new UploadedFile($local_file, 'test.jpg', 'image/jpeg', filesize($local_file), true);
        $postData = [
            'bucket' => $init['bucketName'],
            'old' => 'test.jpg',
            'new' => str_random(10)
        ];
        $response = $this->call('post', 'api/v1/file/create', $postData, [], ['file' => $uploadedFile], $headers);
        $this->seeStatusCode(200);
        $s3Service = new  \App\Services\FileService($init['user']['access_key'], $init['user']['secret_key']);
        $this->post('/api/v1/file/rename', $postData, $headers)->seeStatusCode(200)->seeJsonContains([
            "message" => "Rename File Success"
        ]);
    }
}