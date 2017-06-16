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
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $this->post("/api/v1/admin/setQuota?token={$token}", [
            "email" => str_random(5) . "@imac.com",
            "maxSizeKB" => 10,
            "enabled" => true
        ], [])
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
    public function testSetQuotaButUserIsNotExist()
    {
        $user = $this->initAdmin(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $this->post("/api/v1/admin/setQuota?token={$token}", [
            "email" => str_random(5) . "@imac.com",
            "maxSizeKB" => 10,
            "enabled" => true
        ], [])
        ->seeStatusCode(403)
        ->seeJsonContains([
            "message" => "The user is not exist"
        ]);
    }

    /**
     * Testing the user set quota but the max size is not allowed
     *
     * @return void
     */
    public function testSetQuotaButSizeIsNotAllowed()
    {
        $user = $this->initAdmin(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $email = str_random(5) . "@imac.com";
        $password = str_random(10);
        $this->initUser($email, $password);
        $this->post("/api/v1/admin/setQuota?token={$token}", [
            "email" => $email,
            "maxSizeKB" => -2,
            "enabled" => true
        ], [])
        ->seeStatusCode(403)
        ->seeJsonContains([
            "message" => "Max Size are not allowed"
        ]);
    }

    /**
     * Testing the user set quota but the max size is bigger than variable capacity
     *
     * @return void
     */
    public function testSetQuotaButSizeIsBiggerThanVariable()
    {
        $user = $this->initAdmin(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $email = str_random(5) . "@imac.com";
        $password = str_random(10);
        $this->initUser($email, $password);
        $this->post("/api/v1/admin/setQuota?token={$token}", [
            "email" => $email,
            "maxSizeKB" => 99999999999999999999999999999999999999999,
            "enabled" => true
        ], [])
        ->seeStatusCode(403)
        ->seeJsonContains([
            "message" => "Max size is bigger than variable capacity"
        ]);
    }

    /**
     * Testing the user set quota is successfully
     *
     * @return void
     */
    public function testSetQuotaIsSuccess()
    {
        $user = $this->initAdmin(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $email = str_random(5) . "@imac.com";
        $password = str_random(10);
        $this->initUser($email, $password);
        $this->post("/api/v1/admin/setQuota?token={$token}", [
            "email" => $email,
            "maxSizeKB" => 10,
            "enabled" => true
        ], [])
        ->seeStatusCode(200)
        ->seeJsonContains([
            "message" => "The setting is successfully"
        ]);
    }
}
