<?php

namespace App\Modules\Demo\Models;

use App\Core\BaseModel;


class Members extends BaseModel
{
    protected $tableName = 'members';


    public function __construct()
    {
        parent::__construct();
    }


}
