<?php

namespace App\Modules\Cron\Adapters;

use App\Modules\Cron\Core\Adapter;


class Test extends Adapter
{
    protected $name = 'CRON Test';

    /**
     * Execute the CRON operations.
     */
    public function execute()
    {
        return array($this->name, 'Hello from the CRON Adapter');
    }

}
