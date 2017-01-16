<?php

class DeleteAdminUserTest extends TestCase
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
        $email = $this->postData['email'];
        $this->delete('api/v1/admin/delete/{$email}', [], $headers)
             ->seeStatusCode(403)
             ->seeJsonContains(['message' => 'Permission denied']);
    }
    public function DeleteUserSuccess()
    {
        $init = $this->getAdminToken();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $email = $this->postData['email'];
        $this->delete('api/v1/admin/delete/{$email}', [], $headers)
            ->seeStatusCode(200)
            ->seeJsonContains(['message' => "The user has been deleted."]);
    }
    public function TestUserEmailNoExist()
    {
        $init = $this->getAdminToken();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $email = str_random(10) . '@example.com';
        $this->delete('api/v1/admin/delete/{$email}', [], $headers)
            ->seeStatusCode(403)
            ->seeJsonContains(['message' => "The email does not exist."]);
    }
}
