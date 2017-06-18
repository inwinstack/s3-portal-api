<?php

class DownloadFileTest extends TestCase
{
    /**
     * Testing the user download file is failed.
     *
     * @return void
     */
    public function testDownloadFilefailed()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/bucket/create?token=$admin->token", ["bucket" => $this->bucket]);
        $this->uploadFile($this->bucket,$admin->token);
        $this->get("/api/v1/file/get/$this->bucket/test1.jpg?token=$admin->token")
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "Download file is failed"
            ]);
        $this->delete("/api/v1/bucket/delete/$this->bucket?token=$admin->token");
    }

    /**
     * Testing the user download file is successfully.
     *
     * @return void
     */
    public function testDownloadFileSuccess()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->post("/api/v1/bucket/create?token=$admin->token", ["bucket" => $this->bucket]);
        $this->uploadFile($this->bucket,$admin->token);
        $this->get("/api/v1/file/get/$this->bucket/test.jpg?token=$admin->token")
            ->seeStatusCode(200);
        $this->delete("/api/v1/bucket/delete/$this->bucket?token=$admin->token");
    }
}
