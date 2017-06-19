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
        $user = $this->post('/api/v1/auth/login', $this->user)
            ->response->getData();
        $this->post("/api/v1/admin/role?token=$user->token", [
            "email" => $this->testUser['email'],
            "role" => "admin"])
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
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/admin/role?token=$admin->token", [
            "email" => "root@inwinstack.com",
            "role" => "user"])
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
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/admin/role?token=$admin->token", [
            "email" => $this->testUser['email'],
            "role" => "admin"])
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The user is not exist"
            ]);
        $this->delete("/api/v1/admin/delete/{$this->testUser['email']}?token=$admin->token");
    }

    /**
     * Testing the admin update role is successfully
     *
     * @return void
     */
    public function testUpdateRoleSuccess()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/admin/create?token=$admin->token", $this->testUser);
        $this->post("/api/v1/admin/role?token=$admin->token", [
            "email" => $this->testUser['email'],
            "role" => "admin"])
            ->seeStatusCode(200)
            ->seeJsonStructure(["access_key", "created_at", "email", "id", "name", "role", "secret_key", "uid", "updated_at"]);
        $this->delete("/api/v1/admin/delete/{$this->testUser['email']}?token=$admin->token");
    }
}
