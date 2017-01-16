<?php

class AdminUserListTest extends TestCase
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
        'name' => 'User@imac.com',
        'email' => 'User@imac.com',
        'password' => '123456',
        'password_confirmation' => '123456',
        'role' => 'user'
    ];
    protected $adminData = [
        'name' => 'Admin@imac.com',
        'email' => 'Admin@imac.com',
        'password' => '123456',
        'password_confirmation' => '123456',
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
        $this->get('api/v1/admin/list', $headers)
             ->seeStatusCode(403)
             ->seeJsonContains(['message' => 'Permission denied']);
    }
    public function testAdminUserListSuccess()
    {
        $init = $this->getAdminToken();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $this->get('api/v1/admin/list', $headers)
            ->seeStatusCode(200);
    }
}
