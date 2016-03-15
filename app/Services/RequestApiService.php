<?php

namespace App\Services;

use Curl\Curl;

class RequestApiService
{
    // protected $demos;
    //
    // public function __construct(DemoRepository $demos) {
    //     $this->demos = $demos;
    // }

    public function request($apiMethod, $path, $query, $requestData = [])
    {
        $accessKey = env('AccessKey');
        $secretKey = env('SecretKey');
        $region = env('Region');
        $host = env('ServerURL');
        $adminEntryPoint = env('AdminEntryPoint');
        $requestUrl = "http://$host/$adminEntryPoint/$path$query";

        $dateLong = gmdate('D, d M Y H:i:s T', time());
        $canonical = "$apiMethod\n\n\n$dateLong\n/$adminEntryPoint/$path";

        $signature = base64_encode(hash_hmac('sha1', $canonical, $secretKey, true));

        $curl = new Curl();

        $curl->setHeader('Host', $host);
        $curl->setHeader('Date', $dateLong);
        $curl->setHeader('Authorization', "AWS $accessKey:$signature");
        $curl->$apiMethod($requestUrl, $requestData);

        if ($curl->error) {
            return null;
        } else {
           return  $curl->response;
        }

    }
}
