<?php

class GetUserStateTest extends TestCase
{
    /**
     * Testing the admin set quota is successfully.
     *
     * @return void
     */
     public function testGetUserState()
     {
         $init = $this->initBucket();
         $headers = $this->headers;
         $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
         $this->get('/api/v1/user/state/' . $this->userData['email'], $headers)
             ->seeStatusCode(200)
             ->seeJsonStructure(['totalSizeKB', 'sizePercent', 'maxSizeKB', 'totalObjects', 'objectsPercent', 'maxObjects']);
     }
}
