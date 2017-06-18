<?php

class RenameFolderTest extends TestCase
{
    /**
     * Testing the user rename folder but the bucket is not exist.
     *
     * @return void
     */
    public function testRenameFolderButBucketIsNotExist()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/folder/rename?token=$admin->token", [
            "bucket" => str_random(10),
            "oldName" => str_random(10),
            "newName"=> str_random(10)])
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The bucket is not exist"
            ]);
    }

    /**
     * Testing the user rename folder but the folder of old name is not exist.
     *
     * @return void
     */
    public function testRenameFolderButOldNameFolderIsNotExist()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/bucket/create?token=$admin->token", ["bucket" => $this->bucket]);
        $this->post("/api/v1/folder/rename?token=$admin->token", [
            "bucket" => $this->bucket,
            "oldName" => str_random(10),
            "newName"=> str_random(10)])
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The old name is not exist"
            ]);
        $this->delete("/api/v1/bucket/delete/$this->bucket?token=$admin->token");
    }

    /**
     * Testing the user rename folder but the folder of new name is exist.
     *
     * @return void
     */
    public function testRenameFolderButNewNameFolderIsExist()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/bucket/create?token=$admin->token", ["bucket" => $this->bucket]);
        $this->post("/api/v1/folder/create?token=$admin->token", [
            "bucket" => $this->bucket,
            "prefix" => $this->folder]);
        $this->post("/api/v1/folder/rename?token=$admin->token", [
            "bucket" => $this->bucket,
            "oldName" => $this->folder,
            "newName"=> $this->folder])
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The new name is exist"
            ]);
        $this->delete("/api/v1/bucket/delete/$this->bucket?token=$admin->token");
    }

    /**
     * Testing the user rename folder is successfully.
     *
     * @return void
     */
    public function testRenameFolderSuccess()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/bucket/create?token=$admin->token", ["bucket" => $this->bucket]);
        $this->post("/api/v1/folder/create?token=$admin->token", [
            "bucket" => $this->bucket,
            "prefix" => $this->folder]);
        $this->post("/api/v1/folder/rename?token=$admin->token", [
            "bucket" => $this->bucket,
            "oldName" => $this->folder,
            "newName"=> str_random(10)])
            ->seeStatusCode(200)
            ->seeJsonContains([
                "message" => "The renamed is successfully"
            ]);
        $this->delete("/api/v1/bucket/delete/$this->bucket?token=$admin->token");
    }
}
