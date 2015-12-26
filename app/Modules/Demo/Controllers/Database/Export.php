<?php

namespace App\Modules\Demo\Controllers\Database;

use App\Modules\Demo\Core\BaseController;


class Export extends BaseController
{
    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function mysql()
    {
        $this->title('MySQL Demo Database Export');
    }
    
}
