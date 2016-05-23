<?php
namespace App\Http\Requests\File;

use App\Http\Requests\Request;

/**
 *
 */
class UploadFileRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'bucket' => 'required|max:255',
            'prefix' => 'max:255',
            'file' => 'required|max:10000'
        ];
    }
}


?>
