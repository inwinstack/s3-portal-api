<?php

use Symfony\Component\HttpFoundation\File\UploadedFile;

class CreateFileReplicasTest extends TestCase
{
   /**
    * Testing the user create file replcas is successfully.
    *
    * @return void
    */
   public function testCreateFileReplicas()
   {
      $init = $this->initBucket();
      $headers = $this->headers;
      $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
      $local_file = __DIR__ . '/../test-files/test.jpg';
      $uploadedFile = new UploadedFile($local_file, 'test.jpg', 'image/jpeg', filesize($local_file), true);
      $uploadData = [
          'bucket' => $init['bucketName']
      ];
      $this->call('post', 'api/v1/file/create', $uploadData, [], ['file' => $uploadedFile], $headers);
      $replicasData = [
          'bucket' => $init['bucketName'],
          'file' => 'test.jpg'
      ];
      $this->post("/api/v1/file/replicate", $replicasData, $headers)
          ->seeStatusCode(200)
          ->seeJsonContains([
            "message" => "The replication is complete"
          ]);
   }

    /**
     * Testing the user create file replicas but the file is not exist.
     *
     * @return void
     */
    public function testCreateFileReplicasButNotExist()
    {
        $init = $this->initBucket();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $bucketName = $init['bucketName'];
        $replicasData = [
            'bucket' => $init['bucketName'],
            'file' => 'test.jpg'
        ];
        $this->post('/api/v1/file/replicate', $replicasData, $headers)
            ->seeStatusCode(403)
            ->seeJsonContains([
              "message" => "The file don't exist"
            ]);
    }
}
