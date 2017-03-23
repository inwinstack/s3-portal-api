<?php

class ListUserTest extends TestCase
{
    /**
     * Testing the user see user list but the user do not have permission
     *
     * @return void
     */
    public function testListUserButNotHavePermission()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $this->get("/api/v1/admin/list/1/10?token={$token}", [])
           ->seeStatusCode(403)
           ->seeJsonContains([
             "message" => "Permission denied"
           ]);
    }

    /**
     * Testing the user see user list but the page is less than 0
     *
     * @return void
     */
    public function testListUserButPageIsLessThanZero()
    {
        $user = $this->initAdmin(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $this->get("/api/v1/admin/list/0/10?token={$token}", [])
           ->seeStatusCode(403)
           ->seeJsonContains([
             "message" => "The page value is not incorrect"
           ]);
    }

    /**
     * Testing the user see user list but the count is less than 0
     *
     * @return void
     */
    public function testListUserButCountIsLessThanZero()
    {
        $user = $this->initAdmin(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $this->get("/api/v1/admin/list/1/0?token={$token}", [])
           ->seeStatusCode(403)
           ->seeJsonContains([
             "message" => "The count value is not incorrect"
           ]);
    }

    /**
     * Testing the user see user list is successfully
     *
     * @return void
     */
    public function testListUserSuccess()
    {
        $user = $this->initAdmin(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $this->get("/api/v1/admin/list/1/10?token={$token}", [])
             ->seeStatusCode(200)
             ->seeJsonStructure(["users" => ["*" => ["id", "uid", "name", "role", "email", "access_key", "secret_key", "created_at", "updated_at", "used_size_kb", "total_size_kb"]]]);
    }
}
