<?php

use Illuminate\Database\Seeder;
use App\Services\RequestApiService;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $requestApiService = new RequestApiService();
        $data['uid'] = 'root@inwinstack.com';
        $data['email'] = 'root@inwinstack.com';
        $httpQuery = http_build_query([
            'uid' => $data['uid'],
            'display-name' => $data['email'],
            'email' => $data['email']
        ]);
        $result = json_decode($requestApiService->request('PUT', 'user', "?format=json&$httpQuery"));
        DB::table('users')->insert([
            'uid' => $data['uid'],
            'name' => $data['uid'],
            'role' => 'admin',
            'email' => $data['email'],
            'password' => bcrypt('password'),
            'access_key' => $result->keys[0]->access_key,
            'secret_key' => $result->keys[0]->secret_key,
            'created_at' => '2016-08-16 07:43:01',
            'updated_at' => '2016-08-16 07:43:01'
        ]);
    }
}
