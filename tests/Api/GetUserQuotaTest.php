<?php

class GetUserQuotaTest extends TestCase
{
    /**
     * Testing the user get quota is successfully
     *
     * @return void
     */
    public function testGetQuotaSuccess()
    {
        $this->get("/api/v1/auth/getUserQuota/{$this->admin['email']}")
           ->seeStatusCode(200)
           ->seeJsonStructure(["enabled", "max_objects", "max_size_kb"]);
    }
}
