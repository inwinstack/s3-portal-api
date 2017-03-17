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
        $user = $this->initUser();
        $token = \JWTAuth::fromUser($user);
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $this->delete('/api/v1/bucket/delete/' . str_random(15), [], $headers)
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
         $user = $this->initUser();
         $token = \JWTAuth::fromUser($user);
         $headers = $this->headers;
         $headers['HTTP_Authorization'] = "Bearer $token";
         $bucket = str_random(10);
         $this->createBucket($user, $bucket);
         $this->delete('/api/v1/bucket/delete/' . $bucket, [], $headers)
             ->seeStatusCode(200)
             ->seeJsonContains([
               "message" => "Delete bucket is successfully"
             ]);
     }
}
