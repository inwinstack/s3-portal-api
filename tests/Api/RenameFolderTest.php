<?php

class RenameFolderTest extends TestCase
{
    /**
     * Testing the user rename folder is successfully.
     *
     * @return void
     */
     public function testRenameFolder()
     {
        $init = $this->initBucket();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $createData = [
            'bucket' => $init['bucketName'],
            'prefix' => 'oldName/'
        ];
        $this->post('/api/v1/folder/create', $createData, $headers);
        $renameData = [
            'bucket' => $init['bucketName'],
            'oldName' => 'oldName',
            'newName' => 'newName'
        ];
        $this->post('/api/v1/folder/rename', $renameData, $headers)
            ->seeStatusCode(200)
            ->seeJsonContains([
              'message' => 'The folder is renamed'
            ]);
     }

     /**
      * Testing the user rename folder but the folder is not exist.
      *
      * @return void
      */
     public function testRenameFolderButNotExist()
     {
        $init = $this->initBucket();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $renameData = [
            'bucket' => $init['bucketName'],
            'oldName' => 'oldName',
            'newName' => 'newName'
        ];
        $this->post('/api/v1/folder/rename', $renameData, $headers)
            ->seeStatusCode(403)
            ->seeJsonContains([
              'message' => 'The folder don\'t exist'
            ]);
     }

     /**
      * Testing the user rename folder but the new name is exist.
      *
      * @return void
      */
     public function testRenameFolderButNewNameIsExist()
     {
        $init = $this->initBucket();
        $headers = $this->headers;
        $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
        $createData = [
           'bucket' => $init['bucketName'],
           'prefix' => 'newName/'
        ];
        $this->post('/api/v1/folder/create', $createData, $headers);
        $renameData = [
            'bucket' => $init['bucketName'],
            'oldName' => 'newName',
            'newName' => 'newName'
        ];
        $this->post('/api/v1/folder/rename', $renameData, $headers)
            ->seeStatusCode(403)
            ->seeJsonContains([
                'message' => 'The folder already exists'
            ]);
     }
}
