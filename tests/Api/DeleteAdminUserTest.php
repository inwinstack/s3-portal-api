<?php

class DeleteAdminUserTest extends TestCase
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
        $email = $this->userData['email'];
        $this->delete('api/v1/admin/delete/{$email}', [], $headers)
             ->seeStatusCode(403)
             ->seeJsonContains(['message' => 'Permission denied']);
    }

    /**
     * Testing the administrator delete user is successfully.
     *
     * @return void
     */
    public function DeleteUserSuccess()
    {
        $init = $this->getAdminToken();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $email = $this->userData['email'];
        $this->delete('api/v1/admin/delete/{$email}', [], $headers)
            ->seeStatusCode(200)
            ->seeJsonContains(['message' => "The user has been deleted."]);
    }

    /**
     * Testing administrator delete user but the user is not exist.
     *
     * @return void
     */
    public function TestUserEmailNoExist()
    {
        $init = $this->getAdminToken();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $email = str_random(10) . '@example.com';
        $this->delete('api/v1/admin/delete/{$email}', [], $headers)
            ->seeStatusCode(403)
            ->seeJsonContains(['message' => "The email does not exist."]);
    }
}
