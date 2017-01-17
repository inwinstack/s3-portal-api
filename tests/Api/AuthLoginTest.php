<?php

class AuthLoginTest extends TestCase
{
    /**
     * Testing the user login is successfully.
     *
     * @return void
     */
    public function testLoginSuccess()
    {
        $this->createUser($this->userData['email'], $this->userData['password']);
        $this->post('api/v1/auth/login', $this->userData, $this->headers)
            ->seeStatusCode(200)
            ->seeJsonStructure(['uid', 'token']);
    }

    /**
     * Testing the user login is failed.
     *
     * @return void
     */
    public function testLoginFailed()
    {
        $toValidateData = ['message' => 'verify_error'];
        $this->post('api/v1/auth/login', $this->userData, $this->headers)
            ->seeStatusCode(401)
            ->seeJsonContains($toValidateData);
    }
}
