<?php

namespace App\Services;

use App\Repositories\UserRepository;

class AdminService
{
    protected $ssh;

    public function __construct($ip, $username, $port, $publicKeyPath, $privateKeyPath)
    {
        $this->ssh = ssh2_connect($ip, $port, array('hostkey'=>'ssh-rsa'));
        ssh2_auth_pubkey_file($this->ssh, $username, $publicKeyPath, $privateKeyPath);
    }

    public function capacity()
    {
        $stream = ssh2_exec($this->ssh, 'ceph df -f json');
        stream_set_blocking($stream, true);
        $contents = json_decode(stream_get_contents($stream));
        fclose($stream);
        $capacity['total_bytes'] = $contents->stats->total_bytes;
        $capacity['used_bytes'] = $contents->stats->total_used_bytes;
        $capacity['avail_bytes'] = $contents->stats->total_avail_bytes;
        return $capacity;
    }

    public function listStatus($users, $requestApiService)
    {
        for ($userCount = 0; $userCount < count($users); $userCount++) {
            $sizeKB = 0;
            $userState['users'][$userCount] = $users[$userCount];
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
            $userState['users'][$userCount]['used_size_kb'] = $sizeKB;
            $userState['users'][$userCount]['total_size_kb'] = $userQuota->max_size_kb;
        }
        return $userState;
    }
}
