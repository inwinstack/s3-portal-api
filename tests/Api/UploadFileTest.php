<?php

class UploadFileTest extends TestCase
{
    /**
     * Testing the user upload file but the bucket is not exist.
     *
     * @return void
     */
     public function testUploadFileButBucketIsNotExist()
     {
         $user = $this->initUser();
         $token = \JWTAuth::fromUser($user);
         $headers = $this->headers;
         $headers['HTTP_Authorization'] = "Bearer $token";
         $bucket = str_random(10);
         $response = $this->uploadFile($bucket, $headers);
         $this->assertEquals($response->getStatusCode(), 403);
     }

     /**
      * Testing the user upload file is successfully.
      *
      * @return void
      */
     public function testUploadFileSuccess()
     {
         $user = $this->initUser();
         $token = \JWTAuth::fromUser($user);
         $headers = $this->headers;
         $headers['HTTP_Authorization'] = "Bearer $token";
         $bucket = str_random(10);
         $this->createBucket($user, $bucket);
         $response = $this->uploadFile($bucket, $headers);
         $this->assertEquals($response->getStatusCode(), 200);
     }
}
