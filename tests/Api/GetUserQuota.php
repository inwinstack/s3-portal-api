<?php

class GetUserQuotaTest extends TestCase
{
    /**
     * Testing the user get quota but the user is not exist.
     *
     * @return void
     */
    public function testGetUserQuotaButUserIsNotExist()
    {
        $user = $this->initUser();
        $token = \JWTAuth::fromUser($user);
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $this->get('/api/v1/auth/getUserQuota/NotExistUser@imac.com', [], $headers)
            ->seeStatusCode(403)
            ->seeJsonContains([
              "message" => "The user is not exist"
            ]);
    }

    /**
     * Testing the user get quota is successfully.
     *
     * @return void
     */
    public function testGetUserQuotaSuccess()
    {
        $user = $this->initUser();
        $token = \JWTAuth::fromUser($user);
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $this->get('/api/v1/auth/getUserQuota/' . $user->email, [], $headers)
            ->seeStatusCode(200)
            ->seeJsonStructure(['enabled', 'max_size_kb', 'max_objects']);
    }
}
