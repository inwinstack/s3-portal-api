<?php

class AuthBucketCreateTest extends TestCase
{
    /**
     * Testing the user create bucket is successfully.
     *
     * @return void
     */
    public function testCreateBucketSuccess()
    {
        $init = $this->initBucket();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $bucket = [
            'bucket' => str_random(10)
        ];
        $response = $this->post('/api/v1/bucket/create', $bucket, $headers)
            ->seeStatusCode(200)
            ->seeJsonStructure(['Buckets' => ['*' => ['Name', 'CreationDate']]])
            ->response->content();
        $response = json_decode($response, true);
        $hasName = false;
        foreach ($response['Buckets'] as $value) {
            if ($value['Name'] == $bucket['bucket']) {
                $hasName = true;
            }
        }
        $this->assertTrue($hasName);
    }

    /**
     * Testing the user create bucket but the bucket name has exist.
     *
     * @return void
     */
    public function testCheckBucketIsExist()
    {
        $init = $this->initBucket();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $bucket = [
            'bucket' => str_random(10)
        ];
        $this->post('/api/v1/bucket/create', $bucket, $headers);
        $this->post('/api/v1/bucket/create', $bucket, $headers)
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "Has Bucket"
            ]);
    }

    /**
     * Testing the user create bucket is failed and return cteate bucket error message.
     *
     * @return void
     */
    public function testCreateBucketError()
    {
        $init = $this->initBucket();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $bucket = [
            'bucket' => 'D'
        ];
        $this->post('/api/v1/bucket/create', $bucket, $headers)
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "Create Bucket Error"
            ]);
    }

    /**
     * Testing the user create bucket is failed and return invalid name message.
     *
     * @return void
     */
    public function testCreateBucketInvaidName()
    {
        $init = $this->initBucket();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $bucket = [
            'bucket' => '1'
        ];
        $this->post('/api/v1/bucket/create', $bucket, $headers)
            ->seeStatusCode(403)
            ->seeJsonContains([
                "message" => "Invalid Name"
            ]);
    }
}
