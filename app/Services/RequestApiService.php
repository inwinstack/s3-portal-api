<?php

namespace App\Services;

use Curl\Curl;

class RequestApiService
{
    public function request($apiMethod, $path, $query, $requestData = [])
    {
        $accessKey = env('AccessKey');
        $secretKey = env('SecretKey');
        $region = env('Region');
        $host = env('ServerURL');
        $adminEntryPoint = env('AdminEntryPoint');
        $requestUrl = "http://$host:7480/$adminEntryPoint/$path$query";

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
