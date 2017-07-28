<?php

namespace Backend\Widgets;

use Nova\Support\Facades\View;


class DashboardDummyPanel
{

    public function render(array $data)
    {
        return View::fetch('Backend::Widgets/DashboardPanel', $data);
    }
}
