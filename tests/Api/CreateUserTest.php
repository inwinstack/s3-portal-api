<?php

class CreateUserTest extends TestCase
{
    /**
     * Testing the user create user but the user do not have permission
     *
     * @return void
     */
    public function testCreateUserButNotHavePermission()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $this->post("/api/v1/admin/create?token={$token}", [
          "email" => str_random(5) . "@imac.com",
          "password" => str_random(10)], [])
           ->seeStatusCode(403)
           ->seeJsonContains([
             "message" => "Permission denied"
           ]);
    }

    /**
     * Testing the admin create user but the user is exist
     *
     * @return void
     */
    public function testCreateUserButUserIsExist()
    {
        $user = $this->initAdmin(str_random(5) . "@imac.com", str_random(5));
        $token = \JWTAuth::fromUser($user);
        $email = str_random(5) . "@imac.com";
        $password = str_random(10);
        $this->initUser($email, $password);
        $this->post("/api/v1/admin/create?token={$token}", [
          "email" => $email,
          "password" => $password], [])
           ->seeStatusCode(403)
           ->seeJsonContains([
             "message" => "The user is exist"
           ]);
    }

    /**
     * Testing the admin create user is successfully
     *
     * @return void
     */
    public function testCreateUserSuccess()
    {
        $user = $this->initAdmin(str_random(5) . "@imac.com", str_random(5));
        $token = \JWTAuth::fromUser($user);
        $email = str_random(5) . "@imac.com";
        $password = str_random(10);
        $this->post("/api/v1/admin/create?token={$token}", [
          "email" => $email,
          "password" => $password], [])
           ->seeStatusCode(200)
           ->seeJsonStructure(["uid", "name", "email", "access_key", "secret_key", "role", "updated_at", "created_at", "id"]);
    }
}
