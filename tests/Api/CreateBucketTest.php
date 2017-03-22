<?php

class CreateBucketTest extends TestCase
{
    /**
     * Testing the user create bucket is failed.
     *
     * @return void
     */
    public function testCreateBucketFailed()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $this->post("/api/v1/bucket/create?token={$token}", ["bucket" => "D"], [])
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "Create bucket is failed"
            ]);
    }

    /**
     * Testing the user create bucket but the name is invaid.
     *
     * @return void
     */
    public function testCreateBucketButNameIsInvaid()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $this->post("/api/v1/bucket/create?token={$token}", ["bucket" => "1"], [])
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The bucket name is invalid"
            ]);
    }

    /**
     * Testing the user create bucket but the bucket name is exist.
     *
     * @return void
     */
    public function testCreateBucketButNameIsExist()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $bucket = str_random(10);
        $this->post("/api/v1/bucket/create?token={$token}", ["bucket" => $bucket], []);
        $this->post("/api/v1/bucket/create?token={$token}", ["bucket" => $bucket], [])
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The bucket name is exist"
            ]);
    }

    /**
     * Testing the user create bucket is successfully.
     *
     * @return void
     */
    public function testCreateBucketSuccess()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $this->post("/api/v1/bucket/create?token={$token}", ["bucket" => str_random(10)], [])
            ->seeStatusCode(200)
            ->seeJsonStructure(["Buckets" => ["*" => ["Name", "CreationDate"]]]);
    }
}
