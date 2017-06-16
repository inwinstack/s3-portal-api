<?php
namespace App\Http\Requests\Bucket;

use App\Http\Requests\Request;

/**
 *
 */
class BucketRequest extends Request
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
