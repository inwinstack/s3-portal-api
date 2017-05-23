<?php

namespace App\Services;

use Curl\Curl;

class RequestApiService
{
    public function request($apiMethod, $path, $query, $requestData = [])
    {
        $accessKey = env('S3_ACCESS_KEY');
        $secretKey = env('S3_SECERT_KEY');
        $region = env('REGION');
        $host = env('S3_URL');
        $port = env('S3_PORT');
        $adminEntryPoint = env('S3_ADMIN_ENRTYPOINT');
        $requestUrl = "http://$host:$port/$adminEntryPoint/$path$query";
        $dateLong = gmdate('D, d M Y H:i:s T', time());
        $canonical = "$apiMethod\n\n\n$dateLong\n/$adminEntryPoint/$path";
        $signature = base64_encode(hash_hmac('sha1', $canonical, $secretKey, true));
        $header[] = "Host: $host";
        $header[] = "Date: $dateLong" ;
        $header[] = "Authorization: AWS $accessKey:$signature";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $apiMethod);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}
