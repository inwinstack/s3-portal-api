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
        'name' => 'example',
        'email' => 'example@example.com',
        'password' => 'test1234',
        'password_confirmation' => 'test1234'
    ];

    /**
     *Testing User register email is not taken.
     */
    public function testEmailCheckSuccess()
    {
        $this->post('api/v1/auth/checkEmail', $this->postData, $this->headers)
            ->seeStatusCode(200)
            ->seeJsonContains(['message' => 'GoodJob']);
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

        $toValidateData = [
            'message' => 'has_user'
        ];
        $this->post('api/v1/auth/checkEmail', $this->postData, $this->headers)
            ->seeStatusCode(403)
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