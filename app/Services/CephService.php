<?php

namespace App\Services;

use App\Repositories\UserRepository;

class CephService
{
    protected $ssh;

    public function __construct($ip, $username, $port, $publicKeyPath, $privateKeyPath)
    {
        $this->ssh = ssh2_connect($ip, $port, array('hostkey'=>'ssh-rsa'));
        ssh2_auth_pubkey_file($this->ssh, $username, $publicKeyPath, $privateKeyPath);
    }

    public function listStatus($users, $requestApiService)
    {
        $stream = ssh2_exec($this->ssh, 'ceph df -f json');
        stream_set_blocking($stream, true);
        $contents = json_decode(stream_get_contents($stream));
        fclose($stream);
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
                $userState[$userCount]['total_size_kb'] = $contents->stats->total_avail_bytes;
            } else {
                $userState[$userCount]['total_size_kb'] = $userQuota->max_size_kb;
            }
        }
        return $userState;
    }

    public function totalCapacity()
    {
        $stream = ssh2_exec($this->ssh, 'ceph df -f json');
        stream_set_blocking($stream, true);
        $contents = json_decode(stream_get_contents($stream));
        fclose($stream);
        return $contents->stats->total_avail_bytes;
    }
}
