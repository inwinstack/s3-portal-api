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
        $skip = ($page - 1) * $count;
        return User::skip($skip)->take($count)->get();
    }

    public function getUserCount()
    {
        return User::count();
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

    public function resetPassword($user)
    {
        $password = bcrypt($user['password']);
        User::where('email', $user['email'])
            ->update(['password' => $password]);
        $result = User::where('email', $user['email'])
                      ->where('password', $password)
                      ->first();
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    public function updateRole($user)
    {
        User::where('email', $user['email'])
            ->update(['role' => $user['role']]);
        $result = User::where('email', $user['email'])
                      ->where(['role' => $user['role']])
                      ->first();
        if ($result) {
            return $result;
        } else {
            return false;
        }
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
