<?php

namespace App\Modules\Content\Blocks;

use Nova\Support\Facades\View;

use App\Modules\Content\Blocks\Block;


class Search extends Block
{

    public function render()
    {
        return View::make('Blocks/Search', array(), 'Content')->render();
    }
}
