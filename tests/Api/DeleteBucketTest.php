<?php

class DeleteBucketTest extends TestCase
{
    /**
     * Testing the user delete bucket is successfully.
     *
     * @return void
     */
    public function testDeleteSuccess()
    {
        $init = $this->initBucket();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $bucketName = $init['bucketName'];
        $userData = [
            'bucket' => $bucketName,
            'prefix' => str_random(15)
        ];
        $this->delete('/api/v1/bucket/delete/' . $bucketName, $userData, $headers)
            ->seeStatusCode(200)
            ->seeJsonContains([
              "message" => "Delete Bucket Success"
            ]);
    }

    /**
     * Testing the user delete bucket but the bucket is not exist.
     *
     * @return void
     */
    public function testBucketNotExist()
    {
        $init = $this->initBucket();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $bucketName =str_random(15);
        $userData = [
            'bucket' => $bucketName,
            'prefix' => str_random(15)
        ];
        $this->delete('/api/v1/bucket/delete/' . $bucketName, $userData, $headers)
            ->seeStatusCode(403)
            ->seeJsonContains([
              "message" => "Bucket Non-exist"
            ]);
    }
}
