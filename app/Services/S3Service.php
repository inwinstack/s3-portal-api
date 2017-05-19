<?php

namespace App\Services;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception as S3Exception;

class S3Service
{
    public function connect($accessKey, $secretKey)
    {
        $host = env('S3_URL');
        $port = env('S3_PORT');
        $s3 = S3Client::factory([
            'credentials' => [
                'key'    => $accessKey,
                'secret' => $secretKey,
            ],
            'endpoint' => "http://$host:$port/",
        ]);
        return $s3;
    }
}
