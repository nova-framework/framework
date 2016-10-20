<?php

namespace App\Modules\Demos\Cron\Adapters;

use Nova\Cron\Adapter;


class Test extends Adapter
{
    protected $name = 'CRON Test';

    /**
     * Execute the CRON operations.
     */
    public function handle()
    {
        return 'Hello from the CRON!';
    }

}
