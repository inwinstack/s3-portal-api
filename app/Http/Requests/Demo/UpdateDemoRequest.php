<?php
namespace App\Http\Requests\Demo;

use App\Http\Requests\Request;

/**
 *
 */
class UpdateDemoRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => 'required',
            'demo' => 'required'
        ];
    }
}


?>
