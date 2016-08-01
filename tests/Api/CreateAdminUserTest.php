<?php

class CreateAdminUserTest extends TestCase
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
        'name' => 'ApiTestName',
        'email' => 'ApiTestEmail@yahoo.com.tw',
        'password' => 'ApiTestPassword',
        'password_confirmation' => 'ApiTestPassword',
        'role' => 'user'
    ];
    protected $adminData = [
        'name' => 'backEndApiTestAdmin',
        'email' => 'backEndAdmin@google.com',
        'password' => 'test1234567890admin',
        'password_confirmation' => 'test1234567890admin',
        'role' => 'admin'
    ];
    /**
     *Testing User register email is not taken.
     */
    public function getToken()
    {
        $user = $this->createUser($this->postData['email'], $this->postData['password'], true);
        $token = \JWTAuth::fromUser($user);
        return ['token' => $token, 'user' => $user];
    }
    public function getAdminToken()
    {
        $user = $this->createAdminUser($this->adminData['email'], $this->adminData['password'], true);
        $token = \JWTAuth::fromUser($user);
        return ['token' => $token, 'user' => $user];
    }
    public function testUserCheckNoAdmin()
    {
        $init = $this->getToken();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $this->post('api/v1/admin/create', $this->postData, $headers)
             ->seeStatusCode(403)
             ->seeJsonContains(['message' => 'Permission denied']);
    }
    public function testEmailCheckSuccess()
    {
        $email = $this->postData['email'];
        $this->get('api/v1/auth/checkEmail/{$email}', $this->headers)
            ->seeStatusCode(200)
            ->seeJsonContains(['message' => 'You can use the email']);
    }
    /**
     *Testing User register is Success Action.
     */
    public function testCreateSuccess()
    {
        $init = $this->getAdminToken();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $this->post('api/v1/admin/create', $this->postData, $headers)
            ->seeStatusCode(200)
            ->seeJsonStructure(['uid', 'name']);
    }
    /**
     *Testing User register email is illegal.
     */
    public function testEmailCheckFailed()
    {
        $init = $this->getAdminToken();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $this->post('api/v1/admin/create', $this->adminData, $headers)
            ->seeStatusCode(403)
            ->seeJsonContains(['message' => "The email has already been taken."]);
    }
    /**
     *Testing User register parameter is illegal.
     */
    public function testParamFailed()
    {
        $init = $this->getToken();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $postData = $this->postData;
        unset($postData['email']);
        $data = [];
        $data['message'] = 'validator_error';
        $this->post('api/v1/admin/create', $postData, $headers)
            ->seeStatusCode(422)
            ->seeJsonContains($data);
    }
}