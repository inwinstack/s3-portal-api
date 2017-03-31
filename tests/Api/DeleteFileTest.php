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
          $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
          $token = \JWTAuth::fromUser($user);
          $this->delete("api/v1/file/delete/{str_random(10)}/{str_random(10)}?token={$token}", [], [])
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
          $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
          $token = \JWTAuth::fromUser($user);
          $bucket = str_random(10);
          $this->createBucket($user, $bucket);
          $this->delete("api/v1/file/delete/{$bucket}/{str_random(10)}?token={$token}", [], [])
              ->seeStatusCode(403)
              ->seeJsonContains([
                  "message" => "The file is not exist"
              ]);
      }

      /**
       * Testing the user delete file is successfully.
       *
       * @return void
       */
       public function testDeleteFileSuccess()
       {
           $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
           $token = \JWTAuth::fromUser($user);
           $bucket = str_random(10);
           $this->createBucket($user, $bucket);
           $this->uploadFile($bucket, $token);
           $this->delete("api/v1/file/delete/{$bucket}/test.jpg?token={$token}", [], [])
               ->seeStatusCode(200)
               ->seeJsonContains([
                   "message" => "Delete file is successfully"
               ]);
       }
}
