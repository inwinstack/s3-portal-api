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
        $user = $this->post('/api/v1/auth/login', $this->user)
            ->response->getData();
        $this->post("/api/v1/admin/reset?token=$user->token", $this->testUser)
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
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/admin/reset?token=$admin->token", $this->testUser)
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
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/admin/create?token=$admin->token", $this->testUser);
        $this->post("/api/v1/admin/reset?token=$admin->token", [
            "email" => $this->testUser['email'],
            "password" => str_random(10)])
            ->seeStatusCode(200)
            ->seeJsonStructure(["access_key", "created_at", "email", "id", "name", "role", "secret_key", "uid", "updated_at"]);
        $this->delete("/api/v1/admin/delete/{$this->testUser['email']}?token=$admin->token");
    }
}
