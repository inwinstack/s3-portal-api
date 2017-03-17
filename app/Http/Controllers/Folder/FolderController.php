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
    protected $folderService;
    protected $user;

    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $this->fileService = new FileService($this->user['access_key'], $this->user['secret_key']);
        $this->bucketService = new BucketService($this->user['access_key'], $this->user['secret_key']);
        $this->folderService = new FolderService($this->user['access_key'], $this->user['secret_key']);
    }

    public function store(StoreFolderRequest $request)
    {
        if (!$this->bucketService->check($request->bucket)) {
            return response()->json(['message' => 'The bucket is not exist'], 403);
        }
        if ($this->folderService->checkFolder($request->bucket, $request->prefix)) {
            return response()->json(['message' => 'The folder is exist'], 403);
        }
        if ($this->folderService->store($request->bucket, $request->prefix)) {
            return response()->json(['message' => 'Create folder is Successfully'], 200);
        } else {
            return response()->json(['message' => 'Create folder is failed'], 403);
        }
    }

    public function destroy($bucket, $key)
    {
        if (!$this->bucketService->check($bucket)) {
            return response()->json(['message' => 'The bucket is not exist'], 403);
        }
        if (!$this->folderService->check($bucket, $key)) {
            return response()->json(['message' => 'The folder is not exist'], 403);
        }
        if ($this->folderService->delete($bucket, $key, $this->fileService)) {
            return response()->json(['message' => 'Delete folder is successfully'], 200);
        } else {
            return response()->json(['message' => 'Delete folder is failed'], 403);
        }
    }

    public function rename(Request $request)
    {
        if (!$this->bucketService->check($request->bucket)) {
            return response()->json(['message' => 'The bucket is not exist'], 403);
        }
        if (!$this->folderService->check($request->bucket, $request->oldName)) {
            return response()->json(['message' => 'The old name is not exist'], 403);
        }
        if ($this->folderService->check($request->bucket, $request->newName)) {
            return response()->json(['message' => 'The new name is exist'], 403);
        }
        $renameFolderResponse = $this->folderService->rename($request->bucket, $request->oldName, $request->newName, $this->fileService);
        if ($renameFolderResponse) {
            return response()->json(['message' => 'The renamed is successfully'], 200);
        } else {
            return response()->json(['message' => 'The renamed is failed'], 403);
        }
    }

    public function move(Request $request)
    {
        if (!$this->bucketService->check($request->sourceBucket)) {
            return response()->json(['message' => 'The bucket of source is not exist'], 403);
        }
        if (!$this->bucketService->check($request->goalBucket)) {
            return response()->json(['message' => 'The bucket of goal is not exist'], 403);
        }
        if (!$this->folderService->check($request->sourceBucket, $request->sourceFolder)) {
            return response()->json(['message' => 'The folder of source is not exist'], 403);
        }
        if ($this->folderService->check($request->goalBucket, $request->goalFolder)) {
            return response()->json(['message' => 'The folder of goal is exist'], 403);
        }
        if ($this->folderService->move($request->sourceBucket, $request->sourceFolder, $request->goalBucket, $request->goalFolder, $this->fileService)) {
            return response()->json(['message' => 'The move is complete'], 200);
        } else {
            return response()->json(['message' => 'The move is failed'], 403);
        }
    }
}
