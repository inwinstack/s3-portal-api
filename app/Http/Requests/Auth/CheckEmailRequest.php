<?php
namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;

/**
 *
 */
class CheckEmailRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email|max:255',
        ];
    }
}


?>
