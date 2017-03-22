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
        $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
        $token = \JWTAuth::fromUser($user);
        $this->delete("/api/v1/bucket/delete/{str_random(10)}?token={$token}", [], [])
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
         $user = $this->initUser(str_random(5) . "@imac.com", str_random(10));
         $token = \JWTAuth::fromUser($user);
         $bucket = str_random(10);
         $this->createBucket($user, $bucket);
         $this->delete("/api/v1/bucket/delete/{$bucket}?token={$token}", [], [])
             ->seeStatusCode(200)
             ->seeJsonContains([
               "message" => "Delete bucket is successfully"
             ]);
     }
}
