<?php

namespace App\Modules\Cron\Adapters;

use App\Modules\Cron\Core\Adapter;


class Test extends Adapter
{
    protected $name = 'CRON Test';

    /**
     * Execute the CRON operations.
     */
    protected function handle()
    {
        return 'Hello from the CRON!';
    }

}
