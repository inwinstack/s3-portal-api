<?php

class ResetUserTest extends TestCase
{
    /**
     * Testing the user reset password but the user do not have permission
     *
     * @return void
     */
    public function testResetUserButNotHavePermission()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $this->post("/api/v1/admin/reset?token={$token}", [
            "email" => str_random(5) . "@imac.com",
            "password" => str_random(10)
        ], [])
        ->seeStatusCode(403)
        ->seeJsonContains([
            "message" => "Permission denied"
        ]);
    }

    /**
     * Testing the admin reset password but the user is not exist
     *
     * @return void
     */
    public function testResetUserButUserIsNotExist()
    {
        $user = $this->initAdmin(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $this->post("/api/v1/admin/reset?token={$token}", [
            "email" => str_random(5) . "@imac.com",
            "password" => str_random(10)
        ], [])
        ->seeStatusCode(403)
        ->seeJsonContains([
            "message" => "The user is not exist"
        ]);
    }

    /**
     * Testing the admin reset password is successfully
     *
     * @return void
     */
    public function testResetUserSuccess()
    {
        $user = $this->initAdmin(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $email = str_random(5) . "@imac.com";
        $password = str_random(10);
        $this->initUser($email, $password);
        $this->post("/api/v1/admin/reset?token={$token}", [
            "email" => $email,
            "password" => str_random(10)
        ], [])
        ->seeStatusCode(200)
        ->seeJsonStructure(["access_key", "created_at", "email", "id", "name", "role", "secret_key", "uid", "updated_at"]);
    }
}
