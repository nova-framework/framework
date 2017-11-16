<?php

namespace App\Modules\Content\Widgets;

use Nova\Support\Facades\View;

use Shared\Widgets\Widget;


class Search extends Widget
{

    public function render()
    {
        return View::make('Widgets/Search', array(), 'Content')->render();
    }
}
