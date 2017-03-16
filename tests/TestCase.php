<?php

use App\User;
use Illuminate\Support\Facades\Artisan;

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
     * The base UserData to use while testing.
     *
     * @var array
     */
    protected $userData = [
        'name' => 'UserTest@imac.com',
        'email' => 'UserTest@imac.com',
        'password' => '123456',
        'password_confirmation' => '123456',
        'role' => 'user'
    ];

    /**
     * The base AdminData to use while testing.
     *
     * @var array
     */
    protected $adminData = [
        'name' => 'AdminTest@imac.com',
        'email' => 'AdminTest@imac.com',
        'password' => '123456',
        'password_confirmation' => '123456',
        'role' => 'admin'
    ];

    /**
     * Get user token.
     *
     * @return array
     */
    public function getToken()
    {
        $user = $this->createUser($this->userData['email'], $this->userData['password'], true);
        $token = \JWTAuth::fromUser($user);
        return ['token' => $token, 'user' => $user];
    }

    /**
     * Get administration token.
     *
     * @return array
     */
    public function getAdminToken()
    {
        $user = $this->createAdminUser($this->adminData['email'], $this->adminData['password'], true);
        $token = \JWTAuth::fromUser($user);
        return ['token' => $token, 'user' => $user];
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
     * Create User
     *
     * @param $email
     * @param $password
     * @param bool $hasBucket
     * @return static
     * @internal param bool $hasBucket
     */
    public function createUser($email, $password, $hasBucket = false)
    {
        $httpQuery = http_build_query([
            'uid' => $email,
            'display-name' => $email,
            'email' => $email
        ]);
        $accessKey = str_random(10);
        $secretKey = str_random(10);
        if ($hasBucket) {
            $apiService = new  \App\Services\RequestApiService;
            $result = json_decode($apiService->request('PUT', 'user', "?format=json&$httpQuery"));
            $accessKey = $result->keys[0]->access_key;
            $secretKey = $result->keys[0]->secret_key;
        }

        $userData = [
            'uid' => $email,
            'email' => $email,
            'name' => str_random(4),
            'password' => bcrypt($password),
            'access_key' => $accessKey,
            'secret_key' => $secretKey,
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
    public function createAdminUser($email, $password, $hasBucket = false)
    {
        $httpQuery = http_build_query([
            'uid' => $email,
            'display-name' => $email,
            'email' => $email
        ]);
        $accessKey = str_random(10);
        $secretKey = str_random(10);
        if ($hasBucket) {
            $apiService = new  \App\Services\RequestApiService;
            $result = json_decode($apiService->request('PUT', 'user', "?format=json&$httpQuery"));
            $accessKey = $result->keys[0]->access_key;
            $secretKey = $result->keys[0]->secret_key;
        }
        $userData = [
            'uid' => $email,
            'email' => $email,
            'name' => str_random(4),
            'password' => bcrypt($password),
            'access_key' => $accessKey,
            'secret_key' => $secretKey,
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
     * Initialize the testing.
     *
     * @return array
     */
    protected function initBucket()
    {
        $bucketName = str_random(10);
        $user = $this->createUser($this->userData['email'], $this->userData['password'], true);
        $token = \JWTAuth::fromUser($user);
        $this->createBucket($user, $bucketName);
        return ['bucketName' => $bucketName, 'token' => $token, 'user' => $user];
    }
}
