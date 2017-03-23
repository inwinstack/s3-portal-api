<?php

use App\User;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * The base Headers to use while testing the AuthLoginTest Class.
     *
     * @var array
     */
    protected $headers = [
        'HTTP_Accept' => 'application/json'
    ];

    /**
     * every Test Function to setUp
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->initDatabase();
    }

    /**
     * every Test Function to tearDown
     *
     * @return void
     */
    public function tearDown()
    {
        $this->resetDatabase();
    }

    /**
     * initial database config to sqlite
     *
     * @return void
     */
    public function initDatabase()
    {
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ],
        ]);

        Artisan::call('migrate');
        Artisan::call('db:seed');
    }

    /**
     * call migrate to reset status
     *
     * @return viod
     */
    public function resetDatabase()
    {
        Artisan::call('migrate:reset');
    }

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';
        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        $this->initDatabase();
        return $app;
    }

    /**
     * Create User
     *
     * @param $email
     * @param $password
     * @param bool $hasBucket
     * @return static
     * @internal param bool $hasBucket
     */
    public function initUser($email, $password)
    {
        $httpQuery = http_build_query([
            'uid' => $email,
            'display-name' => $email,
            'email' => $email
        ]);
        $apiService = new  \App\Services\RequestApiService;
        $result = json_decode($apiService->request('PUT', 'user', "?format=json&$httpQuery"));
        $userData = [
            'uid' => $email,
            'email' => $email,
            'name' => str_random(4),
            'password' => bcrypt($password),
            'access_key' => $result->keys[0]->access_key,
            'secret_key' => $result->keys[0]->secret_key,
            'role' => 'user'
        ];
        return User::create($userData);
    }

    /**
     * Create Admin
     *
     * @param $email
     * @param $password
     * @param bool $hasBucket
     * @return static
     * @internal param bool $hasBucket
     */
    public function initAdmin($email, $password)
    {
        $httpQuery = http_build_query([
            'uid' => $email,
            'display-name' => $email,
            'email' => $email
        ]);
        $apiService = new  \App\Services\RequestApiService;
        $result = json_decode($apiService->request('PUT', 'user', "?format=json&$httpQuery"));
        $userData = [
            'uid' => $email,
            'email' => $email,
            'name' => str_random(4),
            'password' => bcrypt($password),
            'access_key' => $result->keys[0]->access_key,
            'secret_key' => $result->keys[0]->secret_key,
            'role' => 'admin'
        ];
        return User::create($userData);
    }

    /**
     * Create Bucket
     *
     * @return void
     */
    protected function createBucket($user, $bucketName)
    {
        $s3Service = new \App\Services\BucketService($user['access_key'], $user['secret_key']);
        $s3Service->create($bucketName);
    }

    /**
     * Create Folder
     *
     * @return void
     */
    protected function createFolder($user, $bucketName, $folderName)
    {
        $folderService = new \App\Services\FolderService($user['access_key'], $user['secret_key']);
        $folderService->store($bucketName, $folderName);
    }

    protected function uploadFile($bucket, $token)
    {
        $headers = $this->headers;
        $headers["HTTP_Authorization"] = "Bearer $token";
        $local_file = __DIR__ . '/test-files/test.jpg';
        $uploadedFile = new UploadedFile($local_file, 'test.jpg', 'image/jpeg', filesize($local_file), true);
        return $this->call('post', 'api/v1/file/create', ['bucket' => $bucket], [], ['file' => $uploadedFile], $headers);
    }
}
