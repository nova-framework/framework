<?php

namespace App\Modules\Content\Blocks;

use Nova\Support\Facades\View;

use Shared\Widgets\Widget;


class Search
{

    public function render()
    {
        return View::make('Blocks/Search', array(), 'Content')->render();
    }
}
