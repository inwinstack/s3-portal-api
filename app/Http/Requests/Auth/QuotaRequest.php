<?php
namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;

/**
 *
 */
class QuotaRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email',
            'maxSizeKB' => 'required',
            'enabled' => 'required'
        ];
    }
}
