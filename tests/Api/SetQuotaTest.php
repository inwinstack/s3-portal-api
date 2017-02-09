<?php

class SetQuotaTest extends TestCase
{
    /**
     * Testing the admin set quota is successfully.
     *
     * @return void
     */
     public function testSetQuota()
     {
         $init = $this->initBucket();
         $headers = $this->headers;
         $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
         $quotaData = [
             'bucket' => 5,
             'max-objects' => 10,
             'max-size-kb' => 10,
             'email' => $this->userData['email'],
             'enabled' => true
         ];
         $this->post('/api/v1/auth/setUserQuota', $quotaData, $headers)
             ->seeStatusCode(200)
             ->seeJsonContains([
                 'message' => 'Setting is successful'
             ]);
     }

     /**
      * Testing the admin set quota but the user is not exist.
      *
      * @return void
      */
      public function testSetQuotaButUserIsNotExist()
      {
          $init = $this->initBucket();
          $headers = $this->headers;
          $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
          $quotaData = [
              'bucket' => 5,
              'max-objects' => 10,
              'max-size-kb' => 10,
              'email' => $this->userData['email'] . 'not',
              'enabled' => true
          ];
          $this->post('/api/v1/auth/setUserQuota', $quotaData, $headers)
              ->seeStatusCode(403)
              ->seeJsonContains([
                  'message' => 'The user is not exist'
              ]);
      }

      /**
       * Testing the admin set quota but max objects or max size are not allowed.
       *
       * @return void
       */
       public function testSetQuotaButMaxObjectOrMaxSizeAreNotAllowed()
       {
           $init = $this->initBucket();
           $headers = $this->headers;
           $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
           $quotaData = [
               'bucket' => 5,
               'max-objects' => -3,
               'max-size-kb' => -3,
               'email' => $this->userData['email'],
               'enabled' => true
           ];
           $this->post('/api/v1/auth/setUserQuota', $quotaData, $headers)
               ->seeStatusCode(403)
               ->seeJsonContains([
                   'message' => 'Max Objects or Max Size are not allowed'
               ]);
       }

       /**
        * Testing the admin set quota but the number of bucket is must be positive.
        *
        * @return void
        */
        public function testSetQuotaButNumberOfBucketIsPositive()
        {
            $init = $this->initBucket();
            $headers = $this->headers;
            $headers['HTTP_Authorization'] = "Bearer {$init['token']}";
            $quotaData = [
                'bucket' => -1,
                'max-objects' => 10,
                'max-size-kb' => 10,
                'email' => $this->userData['email'],
                'enabled' => true
            ];
            $this->post('/api/v1/auth/setUserQuota', $quotaData, $headers)
                ->seeStatusCode(403)
                ->seeJsonContains([
                    'message' => 'The number of buckets must be positive'
                ]);
        }
}
