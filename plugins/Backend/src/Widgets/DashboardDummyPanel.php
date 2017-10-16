<?php

namespace AcmeCorp\Backend\Widgets;

use Nova\Support\Facades\View;


class DashboardDummyPanel
{

    public function render(array $data)
    {
        return View::fetch('AcmeCorp/Backend::Widgets/DashboardPanel', $data);
    }
}
