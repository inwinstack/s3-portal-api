<?php

namespace App\Http\Controllers\File;

use App\Services\FileService;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\File\UploadFileRequest;

use JWTAuth;

class FileController extends Controller
{

    protected $s3Service;
    protected $user;

    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $this->s3Service = new FileService($this->user['access_key'], $this->user['secret_key']);
    }

    public function index(Request $request, $bucket)
    {
        $listResponse = $this->s3Service->listFile($bucket, $request->input('prefix', ''));
        if (!$listResponse) {
            return response()->json(['message' => 'Bucket Error'], 403);
        }
        return response()->json(['files' => $listResponse->get('Contents')], 200);
    }

    public function store(UploadFileRequest $request)
    {
        $uploadResponse = $this->s3Service->uploadFile($request->bucket, $request->file('file')->getPathName(), $request->file('file')->getClientOriginalName(), $request->prefix);
        if ($uploadResponse) {
            return response()->json(['message' => $uploadResponse], 403);
        }
        return response()->json(['message' => 'Upload File Success'], 200);
    }

    public function getFile($bucket, $key)
    {
        $downloadURL = $this->s3Service->getFile($bucket, $key);
        if ($downloadURL) {
            return response()->json(['uri' => $downloadURL], 200);
        }
        return response()->json(['message' => 'Has Error'], 403);
    }
}
