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
        $host = env('S3_URL');
        $port = env('CEPH_REST_API_PORT');
        $ch = curl_init();
        $header[] = "Accept: application/json";
        curl_setopt($ch, CURLOPT_URL, "$host:$port/api/v0.1/df");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        $result = curl_exec($ch);
        curl_close($ch);
        $contents = json_decode($result);
        for ($userCount = 0; $userCount < count($users); $userCount++) {
            $sizeKB = 0;
            $userState[$userCount] = $users[$userCount];
            $uid = $users[$userCount]->uid;
            $userQuota = json_decode($requestApiService->request('GET', 'user', "?quota&uid=$uid&quota-type=user"));
            $bucketList = json_decode($requestApiService->request('GET', 'bucket', "?format=json&uid=$uid"));
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
                $userState[$userCount]['total_size_kb'] = round(($contents->output->stats->total_avail_bytes) / 1024);
            } else {
                $userState[$userCount]['total_size_kb'] = $userQuota->max_size_kb;
            }
        }
        return $userState;
    }

    public function totalCapacity()
    {
        $host = env('S3_URL');
        $port = env('CEPH_REST_API_PORT');
        $ch = curl_init();
        $header[] = "Accept: application/json";
        curl_setopt($ch, CURLOPT_URL, "$host:$port/api/v0.1/status");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        $result = curl_exec($ch);
        curl_close($ch);
        $contents = json_decode($result);
        return $contents->output->pgmap->bytes_avail;
    }
}
