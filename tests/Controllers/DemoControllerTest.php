<?php

class DemoControllerTest extends TestCase
{
    protected $headers = [
        'HTTP_Accept' => 'application/json'
    ];

    protected $repositoryMock = null;

    public function setUp()
    {
        parent::setUp();

        $this->repositoryMock = Mockery::mock('App\Repositories\DemoRepository');
        $this->app->instance('App\Repositories\DemoRepository', $this->repositoryMock);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testIndex()
    {
        $fakeDemos = [];
        for ($i = 0; $i < 5; $i++) {
            $fakeDemos[] = factory('App\Entities\Demo')->make();
        }

        $this->repositoryMock->shouldReceive('getDemoData')
            ->once()
            ->andReturn($fakeDemos);

        $fakeDemos = response()->json($fakeDemos);
        $this->get('demo/read')->json($fakeDemos, 200);
    }

    public function testStore()
    {
        $fakeDemo = factory('App\Entities\Demo')->make();

        $this->repositoryMock
            ->shouldReceive('createByDemo')
            ->once()
            ->andReturn($fakeDemo);

        $data = [
            'demo' => $fakeDemo['demo'],
        ];

        $fakeDemo = response()->json($fakeDemo);
        $this->post('demo/create', $data, $this->headers)->json($fakeDemo, 200);
    }

    public function testUpdate()
    {
        $fakeDemo = factory('App\Entities\Demo')->make();
        $this->repositoryMock
            ->shouldReceive('updateByDemo')
            ->once()
            ->andReturn($fakeDemo);

        $data = [
            'demo' => 'test',
            'id' => 1
        ];

        $response = response()->json(['message' => 'Success']);
        $this->post('demo/update', $data, $this->headers)
            ->json($response, 200);
    }

    public function testDestroy()
    {
        $fakeDemo = factory('App\Entities\Demo')->make();
        $this->repositoryMock
            ->shouldReceive('deleteByDemo')
            ->once()
            ->andReturn($fakeDemo);

        $id = random_int(1, 100);

        $response = response()->json(['message' => 'Success']);
        $this->get("demo/delete/$id")
            ->json($response, 200);
    }
}
