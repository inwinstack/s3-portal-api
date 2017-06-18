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
        $user = $this->post('/api/v1/auth/login', $this->user)
            ->response->getData();
        $this->post("/api/v1/admin/create?token=$user->token", $this->user)
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
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/admin/create?token=$admin->token", $this->user)
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
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/admin/create?token=$admin->token", $this->testUser)
            ->seeStatusCode(200)
            ->seeJsonStructure(["uid", "name", "email", "access_key", "secret_key", "role", "updated_at", "created_at", "id"]);
        $this->delete("/api/v1/admin/delete/{$this->testUser['email']}?token=$admin->token");
    }
}
