<?php

namespace Modules\Content\Blocks;

use Nova\Support\Facades\View;

use Modules\Content\Blocks\Block;


class Search extends Block
{

    public function render()
    {
        return View::make('Blocks/Search', array(), 'Content')->render();
    }
}
