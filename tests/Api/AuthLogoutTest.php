<?php

class AuthLogoutTest extends TestCase
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
        'name' => 'example',
        'email' => 'example@example.com',
        'password' => 'test1234',
        'password_confirmation' => 'test1234'
    ];

    /**
     *Testing User is Logout Success
     */
    public function testLogoutSuccess()
    {
        $token = \JWTAuth::fromUser($this->createUser($this->postData['email'], $this->postData['password']));
        $this->refreshApplication();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $this->post('api/v1/auth/logout', [], $headers)
            ->seeStatusCode(200);
        $this->post('api/v1/auth/logout', [], $headers)
            ->seeStatusCode(500);
    }

    /**
     *Testing User is Logout Failed
     */
    public function testLogoutFailed()
    {
        $token = 'is a error token';
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $this->post('api/v1/auth/logout', [], $headers)
            ->seeStatusCode(500);
    }
}