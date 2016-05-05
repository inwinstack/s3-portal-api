<?php

namespace App\Http\Controllers\File;

use App\Services\S3Service;

use App\Http\Controllers\Controller;

use JWTAuth;

class FileController extends Controller
{

    protected $s3Service;
    protected $user;

    public function __construct(S3Service $s3Service)
    {
        $this->s3Service = $s3Service;
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index($bucket)
    {
        $listResponse = $this->s3Service->listFile($this->user['access_key'], $this->user['secret_key'], $bucket);
        return $listResponse;
        //  var_dump($listResponse) ;
    }

}
