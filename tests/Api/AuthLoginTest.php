<?php


class AuthLoginTest extends TestCase
{
    protected $headers = [
        'HTTP_Accept' => 'application/json'
    ];

    protected $postData = [
        'name' => 'example',
        'email' => 'example@example.com',
        'password' => 'test1234',
        'password_confirmation' => 'test1234'
    ];

    public function setUp()
    {
        parent::setUp();
        $this->initDatabase();
    }

    public function tearDown()
    {
        $this->resetDatabase();
    }

    public function createUser()
    {
        $postData = $this->postData;
        $postData['uid'] = 12;
        $postData['password'] = bcrypt($postData['password']);
        $postData['access_key'] = str_random(10);
        $postData['secret_key'] = str_random(10);

        \App\User::create($postData);
    }

    public function testLoginSuccess()
    {
        $this->createUser();
        $this->post('api/v1/auth/login', $this->postData, $this->headers)->seeStatusCode(200)->seeJsonStructure(['uid', 'token']);
    }

    public function testLoginFailed()
    {
        $json = ['message' => 'verify_error'];
        $this->post('api/v1/auth/login', $this->postData, $this->headers)->seeStatusCode(401)->seeJson($json);
    }

}