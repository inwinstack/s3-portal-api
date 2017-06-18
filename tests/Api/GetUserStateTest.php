<?php

class GetUserStateTest extends TestCase
{
    /**
     * Testing the user get state is successfully
     *
     * @return void
     */
    public function testGetStateSuccess()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->get("/api/v1/user/state?token=$admin->token")
            ->seeStatusCode(200)
            ->seeJsonStructure(["total_size_kb", "max_size_kb", "total_objects", "max_objects"]);
    }
}
