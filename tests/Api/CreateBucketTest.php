<?php

class CreateBucketTest extends TestCase
{
    /**
     * Testing the user create bucket but the name is invaid.
     *
     * @return void
     */
    public function testCreateBucketButNameIsInvaid()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->seeStatusCode(200)
            ->response->getData();
        $this->post("/api/v1/bucket/create?token=$admin->token", ["bucket" => "d"])
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
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->seeStatusCode(200)
            ->response->getData();
        $this->post("/api/v1/bucket/create?token=$admin->token", ["bucket" => $this->bucket]);
        $this->post("/api/v1/bucket/create?token=$admin->token", ["bucket" => $this->bucket])
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The bucket name is exist"
            ]);
        $this->delete("/api/v1/bucket/delete/$this->bucket?token=$admin->token");
    }

    /**
     * Testing the user create bucket is successfully.
     *
     * @return void
     */
    public function testCreateBucketSuccess()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->seeStatusCode(200)
            ->response->getData();
        $this->post("/api/v1/bucket/create?token=$admin->token", ["bucket" => $this->bucket])
            ->seeStatusCode(200)
            ->seeJsonStructure(["Buckets" => ["*" => ["Name", "CreationDate"]]]);
        $this->delete("/api/v1/bucket/delete/$this->bucket?token=$admin->token");
    }
}
