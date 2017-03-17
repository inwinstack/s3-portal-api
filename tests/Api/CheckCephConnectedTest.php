<?php

class CheckCephConnectedTest extends TestCase
{
    /**
     * Testing the user register is successfully.
     *
     * @return void
     */
    public function testRegisterSuccess()
    {
        $this->get('api/v1/auth/checkCephConnected', [], $this->headers)
            ->seeStatusCode(200);
    }
}
