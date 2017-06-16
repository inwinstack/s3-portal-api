<?php
namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;

/**
 *
 */
class RegisterRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ];
    }
}


?>
