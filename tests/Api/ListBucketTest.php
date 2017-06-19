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
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->seeStatusCode(200)
            ->response->getData();
        $this->get("/api/v1/bucket/list?token=$admin->token")
            ->seeStatusCode(200)
            ->seeJsonStructure(["Buckets" => ["*" => ["Name", "CreationDate"]]]);
    }
}
