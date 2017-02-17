<?php

namespace App\Repositories;

use Auth;
use App\User;

/**
 *
 */
class UserRepository
{
    public function getUsers()
    {
        return User::all();
    }

    public function getUser($page, $count)
    {
        $skip = ($page - 1) * 10;
        return User::skip($skip)->take($count)->get();
    }

    public function createUser($userData)
    {
        $userData['password'] = bcrypt($userData['password']);
        $userData['role'] = 'user';
        return User::create($userData);
    }

    public function verify($userData)
    {
        $data = Auth::attempt(['email' => $userData['email'], 'password' => $userData['password']]);
        if ($data) {
            $userData = Auth::User();
            return $userData;
        }
        return null;
    }

    public function check($email)
    {
        return User::where('email', $email)->first();
    }

    public function resetPassword($userData)
    {
        User::where('email', $userData['email'])->update(['password' => bcrypt($userData['password'])]);
        return User::where('email', $userData['email'])->first();
    }

    public function updateRole($userData)
    {
        User::where('email', $userData['email'])->update(['role' => $userData['role']]);
        return User::where('email', $userData['email'])->first();
    }

    public function removeUser($email)
    {
        User::where('email', '=', $email)->delete();
        $data = User::where('email', $email)->first();
        if ($data) {
            return false;
        }
        return true;
    }
}
