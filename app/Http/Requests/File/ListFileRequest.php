<?php
namespace App\Http\Requests\File;

use App\Http\Requests\Request;

/**
 *
 */
class ListFileRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'bucket' => 'required|max:255',
        ];
    }
}


?>
