<?php

class AuthRegisterTest extends TestCase
{
    /**
     * Testing the user email is not used by anynoe.
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
     * Testing the user register is successfully.
     *
     * @return void
     */
    public function testRegisterSuccess()
    {
        $this->post('api/v1/auth/register', $this->userData, $this->headers)
            ->seeStatusCode(200)
            ->seeJsonStructure(['user_id', 'display_name']);
    }

    /**
     * Testing the user email has already exist.
     *
     * @return void
     */
    public function testEmailCheckFailed()
    {
        $this->createUser($this->userData['email'], $this->userData['password']);
        $toValidateData = [];
        $toValidateData['errors']['email'][0] = 'The email has already been taken.';
        $this->post('api/v1/auth/register', $this->userData, $this->headers)
            ->seeStatusCode(422)
            ->seeJsonContains($toValidateData);
    }

    /**
     * Testing the user email is malformed.
     *
     * @return void
     */
    public function testParamFailed()
    {
        $userData = $this->userData;
        unset($userData['email']);
        $data = [];
        $data['message'] = 'validator_error';
        $this->post('api/v1/auth/register', $userData, $this->headers)
            ->seeStatusCode(422)
            ->seeJsonContains($data);
    }
}
