<?php

class ReplicateFileTest extends TestCase
{
    /**
     * Testing the user replicate the file but the bucket is not exist.
     *
     * @return void
     */
    public function testReplicateFileButBucketIsNotExist()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("api/v1/file/replicate?token=$admin->token", [
            "bucket" => str_random(10),
            "file" => str_random(10)])
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The bucket is not exist"
            ]);
    }

    /**
     * Testing the user replicate the file but the file is not exist.
     *
     * @return void
     */
    public function testReplicateFileButFileIsNotExist()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/bucket/create?token=$admin->token", ["bucket" => $this->bucket]);
        $this->post("api/v1/file/replicate?token=$admin->token", [
            "bucket" => $this->bucket,
            "file" => str_random(10)])
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The file is not exist"
            ]);
        $this->delete("/api/v1/bucket/delete/$this->bucket?token=$admin->token");
    }

    /**
     * Testing the user replicate the file but the replicas is exist.
     *
     * @return void
     */
    public function testReplicateFileButReplicasIsExist()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/bucket/create?token=$admin->token", ["bucket" => $this->bucket]);
        $this->uploadFile($this->bucket, $admin->token);
        $this->post("api/v1/file/replicate?token=$admin->token", [
            "bucket" => $this->bucket,
            "file" => "test.jpg"]);
        $this->post("api/v1/file/replicate?token=$admin->token", [
            "bucket" => $this->bucket,
            "file" => "test.jpg"])
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The replicas is exist"
            ]);
        $this->delete("/api/v1/bucket/delete/$this->bucket?token=$admin->token");
    }

    /**
     * Testing the user replicate is successfully.
     *
     * @return void
     */
    public function testReplicateFileSuccess()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/bucket/create?token=$admin->token", ["bucket" => $this->bucket]);
        $this->uploadFile($this->bucket, $admin->token);
        $this->post("api/v1/file/replicate?token=$admin->token", [
            "bucket" => $this->bucket,
            "file" => "test.jpg"])
            ->seeStatusCode(200)
            ->seeJsonContains([
                "message" => "Replication is successfully"
            ]);
        $this->delete("/api/v1/bucket/delete/$this->bucket?token=$admin->token");
    }
}
