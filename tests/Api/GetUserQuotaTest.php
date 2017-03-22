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
        $email = str_random(5) . "@imac.com";
        $user = $this->initUser($email, str_random(10));
        $token = \JWTAuth::fromUser($user);
        $this->get("/api/v1/auth/getUserQuota/{$email}?token={$token}", [], [])
           ->seeStatusCode(200)
           ->seeJsonStructure(["enabled", "max_objects", "max_size_kb"]);
    }
}
