<?php

class MoveFolderTest extends TestCase
{
    /**
     * Testing the user move folder is successfully.
     *
     * @return void
     */
     public function testMoveFolder()
     {
         $init = $this->initBucket();
         $headers = $this->headers;
         $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
         $createData = [
             'bucket' => $init['bucketName'],
             'prefix' => 'sourceFolder/'
         ];
         $this->post('/api/v1/folder/create', $createData, $headers);
         $moveData = [
             'sourceBucket' => $init['bucketName'],
             'sourceFolder' => 'sourceFolder',
             'goalBucket' => $init['bucketName'],
             'goalFolder' => 'goalFolder'
         ];
         $this->post('/api/v1/folder/move', $moveData, $headers)
             ->seeStatusCode(200)
             ->seeJsonContains([
                 'message' => 'The Move is complete'
             ]);
     }

     /**
      * Testing the user move folder but the folder is not exist.
      *
      * @return void
      */
     public function testMoveFolderButNotExist()
     {
         $init = $this->initBucket();
         $headers = $this->headers;
         $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
         $moveData = [
            'sourceBucket' => $init['bucketName'],
            'sourceFolder' => 'sourceFolder',
            'goalBucket' => $init['bucketName'],
            'goalFolder' => 'goalFolder'
         ];
         $this->post('/api/v1/folder/move', $moveData, $headers)
            ->seeStatusCode(403)
            ->seeJsonContains([
                'message' => 'The folder don\'t exist'
            ]);
     }

     /**
      * Testing the user move folder but the new name is exist.
      *
      * @return void
      */
     public function testMoveFolderButNewNameIsExist()
     {
         $init = $this->initBucket();
         $headers = $this->headers;
         $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
         $createData = [
            'bucket' => $init['bucketName'],
            'prefix' => 'test/'
         ];
         $this->post('/api/v1/folder/create', $createData, $headers);
         $moveData = [
            'sourceBucket' => $init['bucketName'],
            'sourceFolder' => 'test',
            'goalBucket' => $init['bucketName'],
            'goalFolder' => 'test'
         ];
         $this->post('/api/v1/folder/move', $moveData, $headers)
            ->seeStatusCode(403)
            ->seeJsonContains([
                'message' => 'The folder already exists'
            ]);
     }

     /**
      * Testing the user use special characters to move folder is failed.
      *
      * @return void
      */
     public function testMoveFolderUseSpecialCharacters()
     {
         $init = $this->initBucket();
         $headers = $this->headers;
         $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
         $createData = [
            'bucket' => $init['bucketName'],
            'prefix' => 'goalFolder/'
         ];
         $this->post('/api/v1/folder/create', $createData, $headers);
         $moveData = [
            'sourceBucket' => $init['bucketName'],
            'sourceFolder' => 'goalFolder',
            'goalBucket' => $init['bucketName'],
            'goalFolder' => '$#@!_'
         ];
         $this->post('/api/v1/folder/move', $moveData, $headers)
            ->seeStatusCode(403)
            ->seeJsonContains([
                'message' => 'The folder move failed'
            ]);
     }
}
