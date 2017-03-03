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
        $this->get('api/v1/admin/list/1', $headers)
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
        $this->get('api/v1/admin/list/1', $headers)
            ->seeStatusCode(200)
            ->seeJsonStructure(['users' => [ '*' => ['id', 'uid', 'name', 'role', 'email', 'access_key', 'secret_key', 'created_at', 'updated_at', 'used_size_kb', 'total_size_kb']], 'count']);
    }

    /**
     * Testing the administrator get user list but the page is not exist.
     *
     * @return void
     */
    public function testAdminUserListButPageIsNoeExist()
    {
        $init = $this->getAdminToken();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $this->get('api/v1/admin/list/99999999999', $headers)
            ->seeStatusCode(200)
            ->seeJsonStructure(['users' => [], 'count']);
    }
}
