<?php

class MoveFolderTest extends TestCase
{
    /**
     * Testing the user move folder but the bucket of source is not exist.
     *
     * @return void
     */
    public function testMoveFolderButSourceBucketIsNotExist()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/folder/move?token=$admin->token", [
            "sourceBucket" => $this->bucket,
            "sourceFolder" => $this->folder,
            "goalBucket" => $this->bucket,
            "goalFolder" => $this->folder])
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The bucket of source is not exist"
            ]);
    }

    /**
     * Testing the user move folder but the bucket of goal is not exist.
     *
     * @return void
     */
    public function testMoveFolderButGoalBucketIsNotExist()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/bucket/create?token=$admin->token", ["bucket" => $this->bucket]);
        $this->post("/api/v1/folder/move?token=$admin->token", [
            "sourceBucket" => $this->bucket,
            "sourceFolder" => $this->folder,
            "goalBucket" => str_random(10),
            "goalFolder" => $this->folder])
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The bucket of goal is not exist"
            ]);
        $this->delete("/api/v1/bucket/delete/$this->bucket?token=$admin->token");
    }

    /**
     * Testing the user move folder but the folder of source is not exist.
     *
     * @return void
     */
    public function testMoveFolderButSourceFolderIsNotExist()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/bucket/create?token=$admin->token", ["bucket" => $this->bucket]);
        $this->post("/api/v1/folder/move?token=$admin->token", [
            "sourceBucket" => $this->bucket,
            "sourceFolder" => str_random(10),
            "goalBucket" => $this->bucket,
            "goalFolder" => str_random(10)])
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The folder of source is not exist"
            ]);
        $this->delete("/api/v1/bucket/delete/$this->bucket?token=$admin->token");
    }

    /**
     * Testing the user move folder but the folder of goal is exist.
     *
     * @return void
     */
    public function testMoveFolderButSourceFolderIsExist()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/bucket/create?token=$admin->token", ["bucket" => $this->bucket]);
        $this->post("/api/v1/folder/create?token=$admin->token", [
            "bucket" => $this->bucket,
            "prefix" => $this->folder]);
        $this->post("/api/v1/folder/move?token=$admin->token", [
            "sourceBucket" => $this->bucket,
            "sourceFolder" => $this->folder,
            "goalBucket" => $this->bucket,
            "goalFolder" => $this->folder])
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The folder of goal is exist"
            ]);
        $this->delete("/api/v1/bucket/delete/$this->bucket?token=$admin->token");
    }

    /**
     * Testing the user move folder is successfully.
     *
     * @return void
     */
    public function testMoveFolderSuccess()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/bucket/create?token=$admin->token", ["bucket" => $this->bucket]);
        $this->post("/api/v1/folder/create?token=$admin->token", [
            "bucket" => $this->bucket,
            "prefix" => $this->folder]);
        $this->post("/api/v1/folder/move?token=$admin->token", [
            "sourceBucket" => $this->bucket,
            "sourceFolder" => $this->folder,
            "goalBucket" => $this->bucket,
            "goalFolder" => str_random(10)])
            ->seeStatusCode(200)
            ->seeJsonContains([
                "message" => "The move is complete"
            ]);
        $this->delete("/api/v1/bucket/delete/$this->bucket?token=$admin->token");
    }
}
