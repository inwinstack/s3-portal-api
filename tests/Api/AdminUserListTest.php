<?php

class AdminUserListTest extends TestCase
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
        $this->get('api/v1/admin/list', $headers)
             ->seeStatusCode(403)
             ->seeJsonContains(['message' => 'Permission denied']);
    }

    /**
     * Testing the administrator successfully get user list.
     *
     * @return void
     */
    public function testAdminUserListSuccess()
    {
        $init = $this->getAdminToken();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $this->get('api/v1/admin/list', $headers)
            ->seeStatusCode(200);
    }
}
