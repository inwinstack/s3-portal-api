<?php

class ResetAdminUserTest extends TestCase
{
    /**
     * Testing the user is not administrator and try to reset user.
     *
     * @return void
     */
    public function testUserCheckNoAdmin()
    {
        $init = $this->getToken();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $this->post('api/v1/admin/reset', $this->userData, $headers)
             ->seeStatusCode(403)
             ->seeJsonContains(['message' => 'Permission denied']);
    }

    /**
     * Testing the user reset password is successfully.
     *
     * @return void
     */
    public function testResetSuccess()
    {
        $init = $this->getAdminToken();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $this->post('api/v1/admin/reset', $this->adminData, $headers)
            ->seeStatusCode(200);
    }

    /**
     * Testing the user email is not exist.
     *
     * @return void
     */
    public function TestUserEmailNoExist()
    {
        $init = $this->getAdminToken();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $userData = [
            $email = str_random(10) . '@example.com',
            $password = bcrypt(str_random(4))
        ];
        $this->post('api/v1/admin/reset', $userData, $headers)
            ->seeStatusCode(403)
            ->seeJsonContains(['message' => "The email does not exist."]);
    }
}
