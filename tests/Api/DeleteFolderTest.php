<?php

class DeleteFolderTest extends TestCase
{
    /**
     * Testing the user delete folder but the bucket is not exist.
     *
     * @return void
     */
    public function testDeleteFolderButBucketIsNotExist()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->delete("/api/v1/folder/delete/{str_random(10)}/{str_random(10)}?token=$admin->token")
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The bucket is not exist"
            ]);
    }

    /**
     * Testing the user delete folder but the folder is not exist.
     *
     * @return void
     */
    public function testDeleteFolderButFolderIsNotExist()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/bucket/create?token=$admin->token", ["bucket" => $this->bucket]);
        $this->delete("/api/v1/folder/delete/{$this->bucket}/{str_random(10)}?token={$admin->token}")
        ->seeStatusCode(403)
        ->seeJsonContains([
            "message" => "The folder is not exist"
        ]);
        $this->delete("/api/v1/bucket/delete/$this->bucket?token=$admin->token");
    }

    /**
     * Testing the user delete folder is successfully.
     *
     * @return void
     */
    public function testDeleteFolderSuccess()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/bucket/create?token=$admin->token", ["bucket" => $this->bucket]);
        $this->post("/api/v1/folder/create?token=$admin->token", [
            "bucket" => $this->bucket,
            "prefix" => $this->folder]);
        $this->delete("/api/v1/folder/delete/{$this->bucket}/{$this->folder}?token={$admin->token}")
            ->seeStatusCode(200)
            ->seeJsonContains([
                "message" => "Delete folder is successfully"
            ]);
        $this->delete("/api/v1/bucket/delete/$this->bucket?token=$admin->token");
    }
}
