<?php

namespace App\Http\Controllers\File;

use App\Services\FileService;
use App\Services\BucketService;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\File\UploadFileRequest;
use App\Http\Requests\File\StoreFolderRequest;

use JWTAuth;

class FileController extends Controller
{
    protected $fileService;
    protected $user;

    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $this->fileService = new FileService($this->user['access_key'], $this->user['secret_key']);
        $this->bucketService = new BucketService($this->user['access_key'], $this->user['secret_key']);
    }

    public function index(Request $request, $bucket)
    {
        if (!$this->bucketService->check($bucket)) {
            return response()->json(['message' => 'The bucket is not exist']);
        }
        $listResponse = $this->fileService->list($bucket, $request->input('prefix', ''));
        if (!$listResponse) {
            return response()->json(['message' => 'List files is failed'], 403);
        }
        return response()->json(['files' => $listResponse->get('Contents')], 200);
    }

    public function store(UploadFileRequest $request)
    {
        if (!$this->bucketService->check($request->bucket)) {
            return response()->json(['message' => 'The bucket is not exist']);
        }
        $uploadResponse = $this->fileService->upload($request->bucket, $request->file('file')->getPathName(), $request->file('file')->getClientOriginalName(), $request->prefix);
        if ($uploadResponse) {
            return response()->json(['message' => 'Upload file is successfully'], 200);
        }
        return response()->json(['message' => 'Upload file is failed'], 403);
    }

    public function get($bucket, $key)
    {
        if (!$this->bucketService->check($bucket)) {
            return response()->json(['message' => 'The bucket is not exist']);
        }
        $explodeString = explode('/', $key);
        $explodeStringCount = count($explodeString);
        $downloadURL = $this->fileService->download($bucket, $key);
        if ($downloadURL) {
            return response()->download(storage_path('tmpfile/' . $downloadURL), $explodeString[$explodeStringCount - 1])->deleteFileAfterSend(true);
        }
        return response()->json(['message' => 'Download file is failed'], 403);
    }

    public function destroy($bucket, $key)
    {
        if (!$this->bucketService->check($bucket)) {
            return response()->json(['message' => 'The bucket is not exist']);
        }
        $deleteFile = $this->fileService->delete($bucket, $key);
        if ($deleteFile) {
            return response()->json(['message' => 'Delete file is successfully'], 200);
        }
        return response()->json(['message' => 'Delete file is failed'], 403);
    }

    public function rename(Request $request)
    {
        if (!$this->bucketService->check($request->bucket)) {
            return response()->json(['message' => 'The bucket is not exist'], 403);
        }
        if (!$this->fileService->check($request->bucket, $request->old)) {
            return response()->json(['message' => 'The file of old name is not exist'], 403);
        }
        if ($this->fileService->check($request->bucket, $request->new)) {
            return response()->json(['message' => 'The file of new name is exist'], 403);
        }
        $renameRespones = $this->fileService->rename($request->bucket, $request->old, $request->new);
        if ($renameRespones) {
            return response()->json(['message' => 'Rename file is Successfully'], 200);
        }
        return response()->json(['message' => 'Rename file is failed'], 403);
    }

    public function move(Request $request)
    {
        if (!$this->bucketService->check($request->sourceBucket)) {
            return response()->json(['message' => 'The bucket of source is not exist'], 403);
        }
        if (!$this->bucketService->check($request->goalBucket)) {
            return response()->json(['message' => 'The bucket of goal is not exist'], 403);
        }
        if (!$this->fileService->check($request->sourceBucket, $request->sourceFile)) {
            return response()->json(['message' => 'The file of source is not exist in source bucket'], 403);
        }
        if ($this->fileService->check($request->goalBucket, $request->goalFile)) {
            return response()->json(['message' => 'The file of goal is exist in goal bucket'], 403);
        }
        $moveResponse = $this->fileService->moveFile($request->sourceBucket, $request->sourceFile, $request->goalBucket, $request->goalFile);
        if ($moveResponse) {
            return response()->json(['message' => 'Move file is successfully'], 200);
        } else {
            return response()->json(['message' => 'Move file is failed'], 403);
        }
    }

    public function replicate(Request $request)
    {
        if (!$this->bucketService->check($request->bucket)) {
            return response()->json(['message' => 'The bucket is not exist'], 403);
        }
        if (!$this->fileService->check($request->bucket, $request->file)) {
            return response()->json(['message' => 'The file is not exist'], 403);
        }
        if ($this->fileService->check($request->bucket, pathinfo($request->file, PATHINFO_FILENAME) . '_copy.' . pathinfo($request->file, PATHINFO_EXTENSION))) {
            return response()->json(['message' => 'The replicas file is exist'], 403);
        }
        $replicateResponse = $this->fileService->replicate($request->bucket, $request->file);
        if ($replicateResponse) {
            return response()->json(['message' => 'Replication is successfully'], 200);
        } else {
            return response()->json(['message' => 'Replication is failed'], 403);
        }
    }
}
