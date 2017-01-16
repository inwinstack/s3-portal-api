<?php
namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;

/**
 *
 */
class AdminRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email|max:255',
            'password' => 'required|min:6'
        ];
    }
}
?>
