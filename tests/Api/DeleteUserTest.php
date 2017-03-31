<?php

class DeleteUserTest extends TestCase
{
    /**
     * Testing the user delete user but the user do not have permission
     *
     * @return void
     */
    public function testDeleteUserButNotHavePermission()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $email = str_random(5) . "@imac.com";
        $this->delete("/api/v1/admin/delete/{$email}?token={$token}", [], [])
           ->seeStatusCode(403)
           ->seeJsonContains([
             "message" => "Permission denied"
           ]);
    }

    /**
     * Testing the admin delete user but the user is root
     *
     * @return void
     */
    public function testDeleteUserButUserIsRoot()
    {
        $user = $this->initAdmin(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $this->delete("/api/v1/admin/delete/root@inwinstack.com?token={$token}", [], [])
           ->seeStatusCode(405)
           ->seeJsonContains([
             "message" => "The root is not allowed to be operated"
           ]);
    }

    /**
     * Testing the admin delete user but the user is not exist
     *
     * @return void
     */
    public function testDeleteUserButUserIsNotExist()
    {
        $user = $this->initAdmin(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $email = str_random(5) . "@imac.com";
        $this->delete("/api/v1/admin/delete/{$email}?token={$token}", [], [])
           ->seeStatusCode(403)
           ->seeJsonContains([
             "message" => "The user is not exist"
           ]);
    }

    /**
     * Testing the admin delete user is successfully
     *
     * @return void
     */
    public function testDeleteUserSuccess()
    {
        $user = $this->initAdmin(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $email = str_random(5) . "@imac.com";
        $this->initUser($email, str_random(10));
        $this->delete("/api/v1/admin/delete/{$email}?token={$token}", [], [])
           ->seeStatusCode(200)
           ->seeJsonContains([
             "message" => "The delete is successfully"
           ]);
    }
}
