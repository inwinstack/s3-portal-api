<?php

class LoginTest extends TestCase
{
    /**
     * Testing the user login is failed.
     *
     * @return void
     */
    public function testLoginFailed()
    {
        $this->post('api/v1/auth/login', ['email' => $this->userData['email'], 'password' => $this->userData['password']], $this->headers)
            ->seeStatusCode(401)
            ->seeJsonContains([
              "message" => "The email is not exist"
            ]);
    }

    /**
     * Testing the user login is successfully.
     *
     * @return void
     */
    public function testLoginSuccess()
    {
        $user = $this->initUser();
        $this->post('api/v1/auth/login', ['email' => $this->userData['email'], 'password' => $this->userData['password']], $this->headers)
            ->seeStatusCode(200)
            ->seeJsonStructure(['id', 'uid', 'name', 'role', 'email', 'access_key', 'secret_key', 'created_at', 'updated_at', 'token']);
    }
}
