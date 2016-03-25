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
     */
    public function createUser($email, $password)
    {
        $userData = [
            'uid' => random_int(1, 100),
            'email' => $email,
            'name' => str_random(4),
            'password' => bcrypt($password),
            'access_key' => str_random(10),
            'secret_key' => str_random(10)
        ];

        User::create($userData);
    }
}
