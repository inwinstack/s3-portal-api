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
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $this->get("/api/v1/user/state?token={$token}", [], [])
           ->seeStatusCode(200)
           ->seeJsonStructure(["total_size_kb", "max_size_kb", "total_objects", "max_objects"]);
    }
}
