<?php
namespace App\Http\Requests\Demo;

use App\Http\Requests\Request;

/**
 *
 */
class CreateDemoRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'demo' => 'required'
        ];
    }
}


?>
