<?php

namespace App\Modules\Demo\Controllers\Database;

use App\Core\ThemedController;

class Export extends ThemedController
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