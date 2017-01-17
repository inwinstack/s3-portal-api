<?php

class AuthLogoutTest extends TestCase
{
    /**
     * Testing the user is Logout Successfully.
     *
     * @return void
     */
    public function testLogoutSuccess()
    {
        $token = \JWTAuth::fromUser($this->createUser($this->userData['email'], $this->userData['password']));
        $this->refreshApplication();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $this->post('api/v1/auth/logout', [], $headers)
            ->seeStatusCode(200);
        $this->post('api/v1/auth/logout', [], $headers)
            ->seeStatusCode(500);
    }

    /**
     * Testing user is logout Failed.
     *
     * @return void
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
