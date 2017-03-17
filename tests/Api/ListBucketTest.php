<?php

class ListBucketTest extends TestCase
{
    /**
     * Testing the user list bucket is successfully.
     *
     * @return void
     */
    public function testListBucketSuccess()
    {
        $user = $this->initUser();
        $token = \JWTAuth::fromUser($user);
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $this->get('/api/v1/bucket/list', $headers)
           ->seeStatusCode(200)
           ->seeJsonStructure(['Buckets' => ['*' => ['Name', 'CreationDate']]]);
    }
}
