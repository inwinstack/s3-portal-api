<?php

class SetQuotaTest extends TestCase
{
    /**
     * Testing the user set quota but the user do not have permission
     *
     * @return void
     */
    public function testSetQuotaButNotHavePermission()
    {
        $user = $this->post('/api/v1/auth/login', $this->user)
            ->response->getData();
        $this->post("/api/v1/admin/setQuota?token=$user->token", [
            "email" => $this->testUser['email'],
            "maxSizeKB" => 10,
            "enabled" => true])
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "Permission denied"
            ]);
    }

    /**
     * Testing the user set quota but the user is not exist
     *
     * @return void
     */
    // public function testSetQuotaButUserIsNotExist()
    // {
    //     $admin = $this->post('/api/v1/auth/login', $this->admin)
    //         ->response->getData();
    //     $this->post("/api/v1/admin/setQuota?token=$admin->token", [
    //         "email" => $this->testUser['email'],
    //         "maxSizeKB" => 10,
    //         "enabled" => true])
    //         ->seeStatusCode(403)
    //         ->seeJsonContains([
    //             "message" => "The user is not exist"
    //         ]);
    // }

    /**
     * Testing the user set quota but the max size is not allowed
     *
     * @return void
     */
    // public function testSetQuotaButSizeIsNotAllowed()
    // {
    //     $admin = $this->post('/api/v1/auth/login', $this->admin)
    //         ->response->getData();
    //     $this->post("/api/v1/admin/create?token=$admin->token", $this->testUser);
    //     $this->post("/api/v1/admin/setQuota?token=$admin->token", [
    //         "email" => $this->testUser['email'],
    //         "maxSizeKB" => -2,
    //         "enabled" => true])
    //         ->seeStatusCode(403)
    //         ->seeJsonContains([
    //             "message" => "Max Size are not allowed"
    //         ]);
    //     $this->delete("/api/v1/admin/delete/{$this->testUser['email']}?token=$admin->token");
    // }

    /**
     * Testing the user set quota but the max size is bigger than variable capacity
     *
     * @return void
     */
    // public function testSetQuotaButSizeIsBiggerThanVariable()
    // {
    //     $admin = $this->post('/api/v1/auth/login', $this->admin)
    //         ->response->getData();
    //     $this->post("/api/v1/admin/create?token=$admin->token", $this->testUser);
    //     $this->post("/api/v1/admin/setQuota?token=$admin->token", [
    //         "email" => $this->testUser['email'],
    //         "maxSizeKB" => 99999999999999999999999999999999999999999,
    //         "enabled" => true])
    //         ->seeStatusCode(403)
    //         ->seeJsonContains([
    //             "message" => "Max size is bigger than variable capacity"
    //         ]);
    //     $this->delete("/api/v1/admin/delete/{$this->testUser['email']}?token=$admin->token");
    // }

    /**
     * Testing the user set quota is successfully
     *
     * @return void
     */
    // public function testSetQuotaIsSuccess()
    // {
    //     $admin = $this->post('/api/v1/auth/login', $this->admin)
    //         ->response->getData();
    //     $this->post("/api/v1/admin/create?token=$admin->token", $this->testUser);
    //     $this->post("/api/v1/admin/setQuota?token=$admin->token", [
    //         "email" => $this->testUser['email'],
    //         "maxSizeKB" => 10,
    //         "enabled" => true])
    //         ->seeStatusCode(200)
    //         ->seeJsonContains([
    //             "message" => "The setting is successfully"
    //         ]);
    //     $this->delete("/api/v1/admin/delete/{$this->testUser['email']}?token=$admin->token");
    // }
}
