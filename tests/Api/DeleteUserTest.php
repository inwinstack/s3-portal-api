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
        $user = $this->post('/api/v1/auth/login', $this->user)
            ->response->getData();
        $this->delete("/api/v1/admin/delete/{$this->testUser['email']}?token=$user->token")
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
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->delete("/api/v1/admin/delete/{$this->admin['email']}?token=$admin->token")
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
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->delete("/api/v1/admin/delete/{$this->testUser['email']}?token=$admin->token")
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
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/admin/create?token=$admin->token", $this->testUser);
        $this->delete("/api/v1/admin/delete/{$this->testUser['email']}?token=$admin->token")
            ->seeStatusCode(200)
            ->seeJsonContains([
                "message" => "The delete is successfully"
            ]);
    }
}
