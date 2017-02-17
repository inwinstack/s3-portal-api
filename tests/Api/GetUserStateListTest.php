<?php

class GetUserStateListTest extends TestCase
{
    /**
     * Testing the user is not administrator and try to get user list.
     *
     * @return void
     */
     public function testUserCheckNoAdmin()
     {
         $init = $this->getToken();
         $headers = $this->headers;
         $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
         $this->get('api/v1/admin/state/1', $headers)
            ->seeStatusCode(403)
            ->seeJsonContains(['message' => 'Permission denied']);
     }

    /**
     * Testing the admin get user state list is successfully.
     *
     * @return void
     */
     public function testAdminGetUserStateList()
     {
         $init = $this->getAdminToken();
         $headers = $this->headers;
         $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
         $this->get('api/v1/admin/state/1', $headers)
             ->seeStatusCode(200)
             ->seeJsonStructure([['uid', 'totalSizeKB', 'sizePercent']]);
     }

    /**
     * Testing the admin get user state list but the page is incorrect.
     *
     * @return void
     */
     public function testAdminGetUserStateListButPageIsIncorrect()
     {
         $init = $this->getAdminToken();
         $headers = $this->headers;
         $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
         $this->get('api/v1/admin/state/0', $headers)
             ->seeStatusCode(403)
             ->seeJsonContains(['message' => 'The page value is not incorrect']);
     }
}
