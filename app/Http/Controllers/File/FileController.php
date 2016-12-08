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

    public function rename(Request $request)
    {
        $renameRespones = $this->s3Service->renameFile($request->bucket, $request->old, $request->new);
        if ($renameRespones) {
            return response()->json(['message' => $renameRespones], 403);
        }
        return response()->json(['message' => 'Rename File Success'], 200);
    }

    public function move(Request $request)
    {
        $moveResponse = $this->s3Service->moveFile($request->sourceBucket, $request->sourceFile, $request->goalBucket, $request->goalFile);
        if ($moveResponse) return response()->json(['message' => 'The Move is complete'], 200);
        else return response()->json(['message' => $moveResponse], 403);
    }

    public function replicate(Request $request)
    {
        $replicateResponse = $this->s3Service->replicateFile($request->bucket, $request->file);
        if (!$replicateResponse) return response()->json(['message' => 'The replication is complete'], 200);
        else return response()->json(['message' => $replicateResponse], 403);
    }
}
