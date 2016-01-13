<?php

namespace App\Modules\Demo\Models;

use App\Core\BaseModel;


class Users extends BaseModel
{
    protected $tableName = 'users';


    public function __construct()
    {
        parent::__construct();
    }


}
