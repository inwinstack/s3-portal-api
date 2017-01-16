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
     */
    public function setUp()
    {
        parent::setUp();
        $this->initDatabase();
    }

    /**
     * every Test Function to tearDown
     */
    public function tearDown()
    {
        $this->resetDatabase();
    }

    /**
     *initial database config to sqlite
     *
     *
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
     *call migrate to reset status
     *
     *
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


}
