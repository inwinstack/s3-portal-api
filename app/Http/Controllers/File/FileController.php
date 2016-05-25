<?php

namespace App\Http\Controllers\File;

use App\Services\FileService;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\File\UploadFileRequest;
use App\Http\Requests\File\StoreFolderRequest;

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
        $explodeString = explode('/', $key);
        $explodeStringCount = count($explodeString);
        $downloadURL = $this->s3Service->getFile($bucket, $key);
        if ($downloadURL) {
            return response()->download(storage_path('tmpfile/' . $downloadURL), $explodeString[$explodeStringCount - 1])->deleteFileAfterSend(true);;
        }
        return response()->json(['message' => 'Has Error'], 403);
    }

    public function storeFolder(StoreFolderRequest $request)
    {
        $storeResponse = $this->s3Service->storeFolder($request->bucket, $request->prefix);
        if ($storeResponse) {
            return response()->json(['message' => $storeResponse], 403);
        }
        return response()->json(['message' => 'Create Folder Success'], 200);
    }

    public function destroy($bucket, $key)
    {
        $deleteFile = $this->s3Service->deleteFile($bucket, $key);
        if ($deleteFile) {
            return response()->json(['message' => $deleteFile], 403);
        }
        return response()->json(['message' => 'Delete File Success'], 200);
    }
}
