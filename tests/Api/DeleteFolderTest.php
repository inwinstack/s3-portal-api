<?php

class DeleteFolderTest extends TestCase
{
    /**
     * Testing the user delete folder but the bucket is not exist.
     *
     * @return void
     */
    public function testDeleteFolderButBucketIsNotExist()
    {
        $user = $this->initUser();
        $token = \JWTAuth::fromUser($user);
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $this->delete('/api/v1/folder/delete/{str_random(10)}/{str_random(10)}', [], $headers)
        ->seeStatusCode(403)
        ->seeJsonContains([
            "message" => "The bucket is not exist"
        ]);
    }

    /**
     * Testing the user delete folder but the folder is not exist.
     *
     * @return void
     */
    public function testDeleteFolderButFolderIsNotExist()
    {
        $user = $this->initUser();
        $token = \JWTAuth::fromUser($user);
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $bucket = str_random(10);
        $this->createBucket($user, $bucket);
        $this->delete('/api/v1/folder/delete/' . $bucket . '/{str_random(10)}', [], $headers)
        ->seeStatusCode(403)
        ->seeJsonContains([
            "message" => "The folder is not exist"
        ]);
    }

    /**
     * Testing the user delete folder is successfully.
     *
     * @return void
     */
    public function testDeleteFolderSuccess()
    {
        $user = $this->initUser();
        $token = \JWTAuth::fromUser($user);
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer $token";
        $bucket = str_random(10);
        $folder = str_random(10);
        $this->createBucket($user, $bucket);
        $this->createFolder($user, $bucket, $folder);
        $this->delete('/api/v1/folder/delete/' . $bucket . '/' . $folder, [], $headers)
        ->seeStatusCode(200)
        ->seeJsonContains([
            "message" => "Delete folder is successfully"
        ]);
    }
}
