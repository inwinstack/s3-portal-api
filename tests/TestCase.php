<?php

use App\User;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    protected $baseUrl = 'http://localhost';

    protected $admin = [
        'email' => 'root@inwinstack.com',
        'password' => 'password'
    ];

    protected $user = [
        'email' => 'imac@imac.com',
        'password' => 'password'
    ];

    protected $testUser = [
        'email' => 'test@imac.com',
        'password' => 'password'
    ];

    protected $bucket = "TestBucket";
    protected $folder = "TestFolder";

    protected $headers = [
        'HTTP_Accept' => 'application/json'
    ];

    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';
        return $app;
    }

    protected function uploadFile($bucket, $token)
    {
        $headers = $this->headers;
        $headers["HTTP_Authorization"] = "Bearer $token";
        $local_file = __DIR__ . '/test-files/test.jpg';
        $uploadedFile = new UploadedFile($local_file, 'test.jpg', 'image/jpeg', filesize($local_file), true);
        return $this->call('post', 'api/v1/file/create', ['bucket' => $bucket], [], ['file' => $uploadedFile], $headers);
    }
}
