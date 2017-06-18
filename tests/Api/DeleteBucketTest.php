<?php

class DeleteBucketTest extends TestCase
{
    /**
     * Testing the user delete bucket but the bucket is not exist.
     *
     * @return void
     */
    public function testDeleteBucketButNameIsNotExist()
    {
        $admin = $this->post('/api/v1/auth/login', $this->admin)
            ->response->getData();
        $this->delete("/api/v1/bucket/delete/$this->bucket?token=$admin->token")
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "The Bucket is not exist"
            ]);
    }

     /**
      * Testing the user delete bucket is successfully.
      *
      * @return void
      */
     public function testDeleteSuccess()
     {
         $admin = $this->post('/api/v1/auth/login', $this->admin)
             ->response->getData();
         $this->post("/api/v1/bucket/create?token=$admin->token", ["bucket" => $this->bucket]);
         $this->delete("/api/v1/bucket/delete/$this->bucket?token=$admin->token")
             ->seeStatusCode(200)
             ->seeJsonContains([
                 "message" => "Delete bucket is successfully"
             ]);
     }
}
