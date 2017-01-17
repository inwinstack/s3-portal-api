<?php

class CreateAdminUserTest extends TestCase
{
    /**
     * Testing the user is not an administrator and does not have permission to create user.
     *
     * @return void
     */
    public function testUserCheckNoAdmin()
    {
        $init = $this->getToken();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $this->post('api/v1/admin/create', $this->userData, $headers)
             ->seeStatusCode(403)
             ->seeJsonContains(['message' => 'Permission denied']);
    }

    /**
     * Testing the administrator email is not used by anynoe.
     *
     * @return void
     */
    public function testEmailCheckSuccess()
    {
        $email = $this->userData['email'];
        $this->get('api/v1/auth/checkEmail/{$email}', $this->headers)
            ->seeStatusCode(200)
            ->seeJsonContains(['message' => 'You can use the email']);
    }

    /**
     * Testing the administrator register is successfully.
     *
     * @return void
     */
    public function testCreateSuccess()
    {
        $init = $this->getAdminToken();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $this->post('api/v1/admin/create', $this->userData, $headers)
            ->seeStatusCode(200)
            ->seeJsonStructure(['uid', 'name']);
    }
    
    /**
     * Testing the user register is failed.
     *
     * @return void
     */
    public function testEmailCheckFailed()
    {
        $init = $this->getAdminToken();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $this->post('api/v1/admin/create', $this->adminData, $headers)
            ->seeStatusCode(403)
            ->seeJsonContains(['message' => "The email has already been taken."]);
    }

    /**
     * Testing administrator email is malformed.
     *
     * @return void
     */
    public function testParamFailed()
    {
        $init = $this->getToken();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $userData = $this->userData;
        unset($userData['email']);
        $data = [];
        $data['message'] = 'validator_error';
        $this->post('api/v1/admin/create', $userData, $headers)
            ->seeStatusCode(422)
            ->seeJsonContains($data);
    }
}
