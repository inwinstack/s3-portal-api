<?php

class AuthRegisterTest extends TestCase
{
    /**
     * The base Headers to use while testing the AuthLoginTest Class.
     *
     * @var array
     */
    protected $headers = [
        'HTTP_Accept' => 'application/json'
    ];
    /**
     * The base PostData to use while testing the AuthLoginTest Class.
     *
     * @var array
     */
    protected $postData = [
        'name' => 'ApiTestName',
        'email' => 'ApiTestEmail@yahoo.com.tw',
        'password' => 'ApiTestPassword',
        'password_confirmation' => 'ApiTestPassword',
        'role' => 'user'
    ];
    /**
     *Testing User register email is not taken.
     */
    public function testEmailCheckSuccess()
    {
        // $query = '?email=' . $this->postData['email'];
        $email = $this->postData['email'];
        $this->get('api/v1/auth/checkEmail/{$email}', $this->headers)
            ->seeStatusCode(200)
            ->seeJsonContains(['message' => 'You can use the email']);
    }
    /**
     *Testing User register is Success Action.
     */
    public function testRegisterSuccess()
    {
        $this->post('api/v1/auth/register', $this->postData, $this->headers)
            ->seeStatusCode(200)
            ->seeJsonStructure(['uid', 'name']);
    }
    /**
     *Testing User register email is illegal.
     */
    public function testEmailCheckFailed()
    {
        $this->createUser($this->postData['email'], $this->postData['password']);
        $toValidateData = [];
        $toValidateData['errors']['email'][0] = 'The email has already been taken.';
        $this->post('api/v1/auth/register', $this->postData, $this->headers)
            ->seeStatusCode(422)
            ->seeJsonContains($toValidateData);
    }
    /**
     *Testing User register parameter is illegal.
     */
    public function testParamFailed()
    {
        $postData = $this->postData;
        unset($postData['email']);
        $data = [];
        $data['message'] = 'validator_error';
        $this->post('api/v1/auth/register', $postData, $this->headers)
            ->seeStatusCode(422)
            ->seeJsonContains($data);
    }
}