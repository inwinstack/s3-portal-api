<?php

class ResetAdminUserTest extends TestCase
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
        $this->post('api/v1/admin/reset', $this->postData, $headers)
             ->seeStatusCode(403)
             ->seeJsonContains(['message' => 'Permission denied']);
    }
    /**
     *Testing User register is Success Action.
     */
    public function testResetSuccess()
    {
        $init = $this->getAdminToken();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $this->post('api/v1/admin/reset', $this->adminData, $headers)
            ->seeStatusCode(200);
    }
    public function TestUserEmailNoExist()
    {
        $init = $this->getAdminToken();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $postData = [
            $email = str_random(10) . '@example.com',
            $password = bcrypt(str_random(4))
        ];
        $this->post('api/v1/admin/reset', $postData, $headers)
            ->seeStatusCode(403)
            ->seeJsonContains(['message' => "The email does not exist."]);
    }
}