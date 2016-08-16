<?php

namespace App\Modules\Cron\Adapters;

use App\Modules\Cron\Core\Adapter;


class Test extends Adapter
{

    /**
     * Execute the Adapter assigned tasks.
     */
    public function execute()
    {
        return 'Hello from the CRON Adapter';
    }

}
