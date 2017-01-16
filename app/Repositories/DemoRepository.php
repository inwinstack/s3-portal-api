<?php

namespace App\Repositories;

use App\Entities\Demo;

/**
 *
 */
class DemoRepository
{
    public function createByDemo($demo)
    {
        return Demo::create($demo);
    }

    public function getDemoData()
    {
        return Demo::all();
    }

    public function updateByDemo($demo)
    {
        return Demo::find($demo['id'])->update(['demo' => $demo['demo']]);
    }

    public function deleteByDemo($id)
    {
        return Demo::destroy($id);
    }
}

?>
