<?php
namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;

/**
 *
 */
class UpdateRoleRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email|max:255',
            'role' => 'required|in:admin,user'
        ];
    }
}
?>
