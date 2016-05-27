<?php
namespace App\Http\Requests\File;

use App\Http\Requests\Request;

/**
 *
 */
class storeFolderRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'bucket' => 'required|max:255',
            'prefix' => 'required|max:255',
        ];
    }
}


?>
