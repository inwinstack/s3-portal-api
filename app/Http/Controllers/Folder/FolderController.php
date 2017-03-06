<?php

namespace App\Http\Controllers\Folder;

use App\Services\FolderService;
use App\Services\FileService;
use App\Services\BucketService;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\File\StoreFolderRequest;

use JWTAuth;

class FolderController extends Controller
{
    protected $s3Service;
    protected $user;

    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $this->fileService = new FileService($this->user['access_key'], $this->user['secret_key']);
        $this->bucketService = new BucketService($this->user['access_key'], $this->user['secret_key']);
        $this->s3Service = new FolderService($this->user['access_key'], $this->user['secret_key']);
    }

    public function store(StoreFolderRequest $request)
    {
        if (!$this->bucketService->checkBucket($request->bucket)) {
            return response()->json(['message' => 'The bucket is not exist'], 403);
        }
        if ($this->s3Service->checkFolder($request->bucket, $request->prefix)) {
            return response()->json(['message' => 'The folder is exist'], 403);
        }
        $storeResponse = $this->s3Service->store($request->bucket, $request->prefix);
        if ($storeResponse) {
            return response()->json(['message' => 'Create folder is Successfully'], 200);
        } else {
            return response()->json(['message' => 'Create folder is failed'], 403);
        }
    }

    public function destroy($bucket, $key)
    {
        if (!$this->bucketService->checkBucket($bucket)) {
            return response()->json(['message' => 'The bucket is not exist'], 403);
        }
        if (!$this->s3Service->checkFolder($bucket, $key)) {
            return response()->json(['message' => 'The folder is not exist'], 403);
        }
        $deleteFolder = $this->s3Service->delete($bucket, $key, $this->fileService);
        if ($deleteFolder) {
            return response()->json(['message' => 'Delete folder is successfully'], 200);
        } else {
            return response()->json(['message' => 'Delete folder is failed'], 403);
        }
    }

    public function rename(Request $request)
    {
        if (!$this->bucketService->checkBucket($request->bucket)) {
            return response()->json(['message' => 'The bucket is not exist'], 403);
        }
        if (!$this->s3Service->checkFolder($request->bucket, $request->oldName)) {
            return response()->json(['message' => 'The old name is not exist'], 403);
        }
        if ($this->s3Service->checkFolder($request->bucket, $request->newName)) {
            return response()->json(['message' => 'The new name is exist'], 403);
        }
        $renameFolderResponse = $this->s3Service->rename($request->bucket, $request->oldName, $request->newName, $this->fileService);
        if ($renameFolderResponse) {
            return response()->json(['message' => 'The renamed is successfully'], 200);
        } else {
            return response()->json(['message' => 'The renamed is failed'], 403);
        }
    }

    public function move(Request $request)
    {
        if (!$this->s3Service->checkFolder($request->sourceBucket, $request->sourceFolder)) {
            return response()->json(['message' => 'The folder of source is not exist'], 403);
        }
        if ($this->s3Service->checkFolder($request->goalBucket, $request->goalFolder)) {
            return response()->json(['message' => 'The folder of goal is exist'], 403);
        }
        $moveFolderResponse = $this->s3Service->move($request->sourceBucket, $request->sourceFolder, $request->goalBucket, $request->goalFolder, $this->fileService);
        if ($moveFolderResponse) {
            return response()->json(['message' => 'The move is complete'], 200);
        } else {
            return response()->json(['message' => 'The move is failed'], 403);
        }
    }
}
