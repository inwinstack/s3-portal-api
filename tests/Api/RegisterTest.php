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
        $this->post("/api/v1/auth/register", [
            "email" => $this->testUser['email'],
            "password" => $this->testUser['password'],
            "password_confirmation" => $this->testUser['password']])
            ->seeStatusCode(200)
            ->seeJsonStructure(["tenant", "user_id", "display_name", "email", "suspended", "max_buckets", "subusers", "keys", "swift_keys", "caps"]);
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->delete("/api/v1/admin/delete/{$this->testUser['email']}?token=$admin->token");
    }
}
