<?php


class AuthRegisterTest extends TestCase
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

    public function testEmailCheckSuccess()
    {
        $this->post('api/v1/auth/checkEmail', $this->postData, $this->headers)->seeStatusCode(200);
    }

    public function testRegisterSuccess()
    {
        $this->post('api/v1/auth/register', $this->postData, $this->headers)->seeStatusCode(200);
    }

    public function testEmailCheckFailed()
    {
        $this->createUser();
        $data = [];
        $data['errors']['email'][0] = 'The email has already been taken.';
//        $this->post('api/v1/auth/checkEmail', $this->postData, $this->headers)->seeJson($data);

//        $this->post('api/v1/auth/register', $this->postData, $this->headers)->seeJson($data);
    }

    public function testParamFailed()
    {
        $postData = $this->postData;
        unset($postData['email']);
        $data = [];
        $data['message'] = 'validator_error';
        $this->post('api/v1/auth/register', $postData, $this->headers)->seeJson($data);
    }


}