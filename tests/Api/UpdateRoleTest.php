<?php

class UpdateRoleTest extends TestCase
{
    /**
     * Testing the user update role but the user do not have permission
     *
     * @return void
     */
    public function testUpdateRoleButNotHavePermission()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $this->post("/api/v1/admin/role?token={$token}", [
            "email" => str_random(5) . "@imac.com",
            "role" => "admin"
        ], [])
        ->seeStatusCode(403)
        ->seeJsonContains([
            "message" => "Permission denied"
        ]);
    }

    /**
     * Testing the admin update role but the user is root
     *
     * @return void
     */
    public function testUpdateRoleButUserIsRoot()
    {
        $user = $this->initAdmin(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $this->post("/api/v1/admin/role?token={$token}", [
            "email" => "root@inwinstack.com",
            "role" => "user"
        ], [])
        ->seeStatusCode(405)
        ->seeJsonContains([
            "message" => "The root is not allowed to be operated"
        ]);
    }

    /**
     * Testing the admin update role but the user is not exist
     *
     * @return void
     */
    public function testUpdateRoleButUserIsNotExist()
    {
        $user = $this->initAdmin(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $this->post("/api/v1/admin/role?token={$token}", [
            "email" => str_random(5) . "@imac.com",
            "role" => "admin"
        ], [])
        ->seeStatusCode(403)
        ->seeJsonContains([
            "message" => "The user is not exist"
        ]);
    }

    /**
     * Testing the admin update role is successfully
     *
     * @return void
     */
    public function testUpdateRoleSuccess()
    {
        $user = $this->initAdmin(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $email = str_random(5) . "@imac.com";
        $password = str_random(10);
        $this->initUser($email, $password);
        $this->post("/api/v1/admin/role?token={$token}", [
            "email" => $email,
            "role" => "admin"
        ], [])
        ->seeStatusCode(200)
        ->seeJsonStructure(["access_key", "created_at", "email", "id", "name", "role", "secret_key", "uid", "updated_at"]);
    }
}
