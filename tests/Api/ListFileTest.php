<?php

class ListFileTest extends TestCase
{
    /**
     * Testing the user list file but the bucket is not exist.
     *
     * @return void
     */
    public function testListFileButBucketIsNotExist()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->get("api/v1/file/list/$this->bucket?token=$admin->token")
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The bucket is not exist"
            ]);
    }

    /**
     * Testing the user list file is successfully.
     *
     * @return void
     */
    public function testListFileSuccess()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/bucket/create?token=$admin->token", ["bucket" => $this->bucket]);
        $this->get("api/v1/file/list/$this->bucket?token=$admin->token")
            ->seeStatusCode(200)
            ->seeJsonStructure(["files"]);
        $this->delete("/api/v1/bucket/delete/$this->bucket?token=$admin->token");
    }
}
