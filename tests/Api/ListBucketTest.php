<?php

class ListBucketTest extends TestCase
{
    /**
     * Testing the user list bucket is successfully.
     *
     * @return void
     */
    public function testListBucketSuccess()
    {
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $headers = $this->headers;
        $headers["HTTP_Authorization"] = "Bearer $token";
        $this->createBucket($user, str_random(5));
        $this->get("/api/v1/bucket/list", $headers)
           ->seeStatusCode(200)
           ->seeJsonStructure(["Buckets" => ["*" => ["Name", "CreationDate"]]]);
    }
}
