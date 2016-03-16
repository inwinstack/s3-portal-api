<?php

namespace App\Repositories;

use Auth;
use App\User;

/**
 *
 */
class UserRepository
{
    public function createUser($userData)
    {
        $userData['password'] = bcrypt($userData['password']);
        return User::create($userData);
    }

    public function verify($userData)
    {
        $data = Auth::attempt(['email' => $userData['email'], 'password' => $userData['password']]);
        if ($userData) {
            $userData = Auth::User();
            return $userData;
        }
        return null;
    }

}

?>
