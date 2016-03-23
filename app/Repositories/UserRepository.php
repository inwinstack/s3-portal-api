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

    public function check($email)
    {
        return User::where('email', $email)->first();
    }
    // public function getDemoData()
    // {
    //     return Demo::all();
    // }
    //
    // public function updateByDemo($demo)
    // {
    //     return Demo::find($demo['id'])->update(['demo' => $demo['demo']]);
    // }
    //
    // public function deleteByDemo($id)
    // {
    //     return Demo::destroy($id);
    // }
}

?>
