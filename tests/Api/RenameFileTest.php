<?php

class RenameFileTest extends TestCase
{
    /**
     * Testing the user rename the file but the bucket is not exist.
     *
     * @return void
     */
    public function testRenameFileButBucketIsNotExist()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("api/v1/file/rename?token=$admin->token", [
            "bucket" => str_random(10),
            "old" => str_random(10),
            "new" => str_random(10)])
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The bucket is not exist"
            ]);
    }

    /**
     * Testing the user rename the file but the file of old is not exist.
     *
     * @return void
     */
    public function testRenameFileButOldFileIsNotExist()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/bucket/create?token=$admin->token", ["bucket" => $this->bucket]);
        $this->post("api/v1/file/rename?token=$admin->token", [
            "bucket" => $this->bucket,
            "old" => str_random(10),
            "new" => str_random(10)])
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The file of old name is not exist"
            ]);
        $this->delete("/api/v1/bucket/delete/$this->bucket?token=$admin->token");
    }

    /**
     * Testing the user rename the file but the file of new is exist.
     *
     * @return void
     */
    public function testRenameFileButNewFileIsExist()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/bucket/create?token=$admin->token", ["bucket" => $this->bucket]);
        $this->uploadFile($this->bucket, $admin->token);
        $this->post("api/v1/file/rename?token=$admin->token", [
            "bucket" => $this->bucket,
            "old" => "test.jpg",
            "new" => "test.jpg"])
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The file of new name is exist"
            ]);
        $this->delete("/api/v1/bucket/delete/$this->bucket?token=$admin->token");
    }

    /**
     * Testing the user rename the file is successfully.
     *
     * @return void
     */
    public function testRenameFileSuccess()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/bucket/create?token=$admin->token", ["bucket" => $this->bucket]);
        $this->uploadFile($this->bucket, $admin->token);
        $this->post("api/v1/file/rename?token=$admin->token", [
            "bucket" => $this->bucket,
            "old" => "test.jpg",
            "new" => "test2.jpg"])
            ->seeStatusCode(200)
            ->seeJsonContains([
                "message" => "Rename file is Successfully"
            ]);
        $this->delete("/api/v1/bucket/delete/$this->bucket?token=$admin->token");
    }
}
