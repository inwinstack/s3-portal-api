<?php

class MoveFileTest extends TestCase
{
    /**
     * Testing the user move the file but the bucket of source is not exist.
     *
     * @return void
     */
    public function testMoveFileButSourceBucketIsNotExist()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("api/v1/file/move?token=$admin->token", [
            "sourceBucket" => str_random(10),
            "goalBucket" => str_random(10),
            "sourceFile" => "source",
            "goalFile" => "goal"])
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The bucket of source is not exist"
            ]);
    }

    /**
     * Testing the user move the file but the bucket of goal is not exist.
     *
     * @return void
     */
    public function testMoveFileButGoalBucketIsNotExist()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/bucket/create?token=$admin->token", ["bucket" => $this->bucket]);
        $this->post("api/v1/file/move?token=$admin->token", [
            "sourceBucket" => $this->bucket,
            "goalBucket" => str_random(10),
            "sourceFile" => "source",
            "goalFile" => "goal"])
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The bucket of goal is not exist"
            ]);
        $this->delete("/api/v1/bucket/delete/$this->bucket?token=$admin->token");
    }

    /**
     * Testing the user move the file but the file of source is not exist in source bucket.
     *
     * @return void
     */
    public function testMoveFileButSourceFileIsNotExist()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/bucket/create?token=$admin->token", ["bucket" => $this->bucket]);
        $this->post("api/v1/file/move?token=$admin->token", [
            "sourceBucket" => $this->bucket,
            "goalBucket" => $this->bucket,
            "sourceFile" => "source",
            "goalFile" => "goal"])
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The file of source is not exist in source bucket"
            ]);
        $this->delete("/api/v1/bucket/delete/$this->bucket?token=$admin->token");
    }

    /**
     * Testing the user move the file but the file of goal is exist in goal bucket.
     *
     * @return void
     */
    public function testMoveFileButGoalFileIsExist()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/bucket/create?token=$admin->token", ["bucket" => $this->bucket]);
        $this->uploadFile($this->bucket,$admin->token);
        $this->post("/api/v1/file/move?token=$admin->token", [
            "sourceBucket" => $this->bucket,
            "goalBucket" => $this->bucket,
            "sourceFile" => "test.jpg",
            "goalFile" => "test.jpg"])
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The file of goal is exist in goal bucket"
            ]);
        $this->delete("/api/v1/bucket/delete/$this->bucket?token=$admin->token");
    }

    /**
     * Testing the user move the file is successfully.
     *
     * @return void
     */
    public function testMoveFileSuccess()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/bucket/create?token=$admin->token", ["bucket" => $this->bucket]);
        $this->uploadFile($this->bucket,$admin->token);
        $this->post("api/v1/file/move?token=$admin->token", [
              "sourceBucket" => $this->bucket,
              "goalBucket" => $this->bucket,
              "sourceFile" => "test.jpg",
              "goalFile" => "test2.jpg"])
              ->seeStatusCode(200)
              ->seeJsonContains([
                  "message" => "Move file is successfully"
              ]);
        $this->delete("/api/v1/bucket/delete/$this->bucket?token=$admin->token");
    }
}
