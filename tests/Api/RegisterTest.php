<?php

class RegisterTest extends TestCase
{
    /**
     * Testing the user register is successfully.
     *
     * @return void
     */
    public function testRegisterSuccess()
    {
        $this->post('api/v1/auth/register', $this->userData, $this->headers)
            ->seeStatusCode(200)
            ->seeJsonStructure(['tenant', 'user_id', 'display_name', 'email', 'suspended', 'max_buckets', 'subusers', 'keys', 'swift_keys', 'caps']);
    }
}
