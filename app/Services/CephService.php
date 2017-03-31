<?php

namespace App\Services;

use App\Repositories\UserRepository;

class CephService
{
    public function __construct()
    {
    }

    public function listStatus($users, $requestApiService)
    {
        $host = env('ServerURL');
        $ch = curl_init();
        $header[] = "Accept: application/json";
        curl_setopt($ch, CURLOPT_URL, "$host:5000/api/v0.1/status");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        $result = curl_exec($ch);
        curl_close($ch);
        $contents = json_decode($result);
        for ($userCount = 0; $userCount < count($users); $userCount++) {
            $sizeKB = 0;
            $userState[$userCount] = $users[$userCount];
            $userQuota = json_decode($requestApiService->request('GET', 'user', "?quota&uid=" . $users[$userCount]->uid . "&quota-type=user"));
            $bucketList = json_decode($requestApiService->request('GET', 'bucket', '?format=json&uid=' . $users[$userCount]->uid));
            for ($bucketCount = 0; $bucketCount < count($bucketList); $bucketCount++) {
                $httpQuery = http_build_query([
                    'bucket' => $bucketList[$bucketCount]
                ]);
                $bucket = json_decode($requestApiService->request('GET', 'bucket', "?$httpQuery"));
                if (!empty((array)($bucket->usage))) {
                    $sizeKB += intval($bucket->usage->{'rgw.main'}->size_kb);
                }
            }
            $userState[$userCount]['used_size_kb'] = $sizeKB;
            if ($userQuota->max_size_kb == -1) {
                $userState[$userCount]['total_size_kb'] = round(($contents->output->pgmap->bytes_avail) / 1024);
            } else {
                $userState[$userCount]['total_size_kb'] = $userQuota->max_size_kb;
            }
        }
        return $userState;
    }

    public function totalCapacity()
    {
        $host = env('ServerURL');
        $ch = curl_init();
        $header[] = "Accept: application/json";
        curl_setopt($ch, CURLOPT_URL, "$host:5000/api/v0.1/status");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        $result = curl_exec($ch);
        curl_close($ch);
        $contents = json_decode($result);
        return $contents->output->pgmap->bytes_avail;
    }
}
