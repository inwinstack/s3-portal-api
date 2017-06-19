<?php

class DeleteFileTest extends TestCase
{
    /**
      * Testing the user delete file but the bucket is not exist.
      *
      * @return void
      */
      public function testDeleteFileButBucketIsNotExist()
      {
          $admin = $this->post('/api/v1/auth/login', $this->admin)
              ->response->getData();
          $this->delete("api/v1/file/delete/{str_random(10)}/{str_random(10)}?token=$admin->token")
              ->seeStatusCode(403)
              ->seeJsonContains([
                  "message" => "The bucket is not exist"
              ]);
      }

     /**
      * Testing the user delete file but the file is not exist.
      *
      * @return void
      */
      public function testDeleteFileButFileIsNotExist()
      {
          $admin = $this->post('/api/v1/auth/login', $this->admin)
              ->response->getData();
          $this->post("/api/v1/bucket/create?token=$admin->token", ["bucket" => $this->bucket]);
          $this->delete("api/v1/file/delete/$this->bucket/{str_random(10)}?token=$admin->token")
              ->seeStatusCode(403)
              ->seeJsonContains([
                  "message" => "The file is not exist"
              ]);
          $this->delete("/api/v1/bucket/delete/$this->bucket?token=$admin->token");
      }

      /**
       * Testing the user delete file is successfully.
       *
       * @return void
       */
       public function testDeleteFileSuccess()
       {
            $admin = $this->post('/api/v1/auth/login', $this->admin)
                ->response->getData();
            $this->post("/api/v1/bucket/create?token=$admin->token", ["bucket" => $this->bucket]);
            $this->uploadFile($this->bucket, $admin->token);
            $this->delete("api/v1/file/delete/$this->bucket/test.jpg?token=$admin->token")
                ->seeStatusCode(200)
                ->seeJsonContains([
                   "message" => "Delete file is successfully"
                ]);
            $this->delete("/api/v1/bucket/delete/$this->bucket?token=$admin->token");
       }
}
