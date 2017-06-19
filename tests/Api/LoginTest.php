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
        $this->post("api/v1/auth/login", $this->testUser)
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
        $this->post("api/v1/auth/login", $this->admin)
            ->seeStatusCode(200)
            ->seeJsonStructure(
                ['id', 'uid', 'name', 'role', 'email', 'access_key', 'secret_key', 'created_at', 'updated_at', 'token']
            );
    }
}
