<?php

namespace App\Modules\Contacts\Blocks;

use Nova\Support\Facades\Request;
use Nova\Support\Facades\View;

use App\Modules\Content\Blocks\Block;


class Message extends Block
{

    public function render()
    {
        $path = Request::path();

        return View::make('Blocks/Message', compact('path'), 'Contacts')->render();
    }
}
