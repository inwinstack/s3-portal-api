<?php

namespace App\Http\Controllers\Demo;

use App\Http\Requests\Demo\CreateDemoRequest;
use App\Http\Requests\Demo\UpdateDemoRequest;
use App\Repositories\DemoRepository;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class DemoController extends Controller
{
    protected $demos;

    public function __construct(DemoRepository $demos) {
        $this->demos = $demos;
    }

    public function index()
    {
        $data = $this->demos->getDemoData();
        return response()->json($data);
    }

    public function store(CreateDemoRequest $request)
    {
        $demo = $this->demos->createByDemo($request->only('demo'));
        return response()->json($demo);
    }

    public function update(UpdateDemoRequest $request)
    {
        $demo = $this->demos->updateByDemo($request->all());
        if ($demo) {
            return response()->json(['message' => 'Success'], 200);
        }
        return response()->json(['message' => 'Error'], 400);
    }

    public function destroy($id)
    {
        $demo = $this->demos->deleteByDemo($id);
        if ($demo) {
            return response()->json(['message' => 'Success'], 200);
        }
        return response()->json(['message' => 'Error'], 400);
    }
}
