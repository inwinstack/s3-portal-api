<?php

class CreateFolderTest extends TestCase
{
    /**
     * Testing the user create folder but the bucket is not exist.
     *
     * @return void
     */
    public function testCreateFolderButBucketIsNotExist()
    {
        $user = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/folder/create?token=$user->token", [
            "bucket" => $this->bucket,
            "prefix" => $this->folder])
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The bucket is not exist"
            ]);
    }

    /**
     * Testing the user create folder but the folder is exist.
     *
     * @return void
     */
    public function testCreateFolderButFolderIsExist()
    {
        $user = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/bucket/create?token=$user->token", ["bucket" => $this->bucket]);
        $this->post("/api/v1/folder/create?token=$user->token", [
            "bucket" => $this->bucket,
            "prefix" => $this->folder]);
        $this->post("/api/v1/folder/create?token=$user->token", [
            "bucket" => $this->bucket,
            "prefix" => $this->folder])
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The folder is exist"
            ]);
        $this->delete("/api/v1/bucket/delete/$this->bucket?token=$user->token");
    }

    /**
     * Testing the user create folder is successfully.
     *
     * @return void
     */
    public function testCreateFolderSuccess()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/bucket/create?token=$admin->token", ["bucket" => $this->bucket]);
        $this->post("/api/v1/folder/create?token=$admin->token", [
            "bucket" => $this->bucket,
            "prefix" => $this->folder])
            ->seeStatusCode(200)
            ->seeJsonContains([
                "message" => "Create folder is Successfully"
            ]);
        $this->delete("/api/v1/bucket/delete/$this->bucket?token=$admin->token");
    }
}
