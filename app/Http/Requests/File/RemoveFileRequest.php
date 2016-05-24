<?php
namespace App\Http\Requests\File;

use App\Http\Requests\Request;

/**
 *
 */
class RemoveFileRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'bucket' => 'required|max:255',
            'key' => 'required|max:255',
        ];
    }
}


?>
