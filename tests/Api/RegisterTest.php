<?php

class RegisterTest extends TestCase
{
    /**
     * Testing the user register is successfully.
     *
     * @return void
     */
    public function testRegisterSuccess()
    {
        $password = str_random(10);
        $this->post("api/v1/auth/register", [
            "email" => str_random(5) . "@imac.com",
            "password" => $password,
            "password_confirmation" => $password
        ], [])
        ->seeStatusCode(200)
        ->seeJsonStructure(["tenant", "user_id", "display_name", "email", "suspended", "max_buckets", "subusers", "keys", "swift_keys", "caps"]);
    }
}
