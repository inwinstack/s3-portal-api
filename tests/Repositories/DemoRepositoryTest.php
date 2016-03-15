<?php

/**
 * Created by PhpStorm.
 * User: user
 * Date: 2016/1/25
 * Time: 下午 01:53
 */
use App\Repositories\DemoRepository;

class DemoRepositoryTest extends TestCase
{

    protected $repository = null;

    public function setUp()
    {
        parent::setUp();
        $this->initDatabase();
        $this->repository = new DemoRepository();
    }

    public function tearDown()
    {
        $this->resetDatabase();
    }

    public function testGetDemoData()
    {
//        $this->resetDatabase();
//        $this->initDatabase();

        $fakeData = [];
        for ($i = 0; $i < 5; $i++) {
            $fakeData[] = factory('App\Entities\Demo')->create()->toArray();
        }

        $this->assertEquals($this->repository->getDemoData()->toArray(), $fakeData);
    }


    public function testCreateByDemo()
    {
        $data = [
            'demo' => 'testing'
        ];

        $createDone = $this->repository->createByDemo($data);
        $this->assertEquals($data['demo'], $createDone->demo);
    }

    public function testUpdateByDemo()
    {
        $demo = factory('App\Entities\Demo')->create()->toArray();

        $data = [
            'id' => $demo['id'],
            'demo' => 'i love yang'
        ];

        $updateDone = $this->repository->updateByDemo($data);
        $this->assertEquals(true, $updateDone);
    }

    public function testDeleteByDemo()
    {
        $demo = factory('App\Entities\Demo')->create()->toArray();

        $updateDone = $this->repository->deleteByDemo($demo['id']);
        $this->assertEquals(true, $updateDone);
    }

}
