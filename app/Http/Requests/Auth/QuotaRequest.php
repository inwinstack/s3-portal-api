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
            'email' => 'required|email|max:255',
            'bucket' => 'required|max:10',
            'max-objects' => 'required|max:10',
            'max-size-kb' => 'required|max:6',
            'enabled' => 'required'
        ];
    }
}
