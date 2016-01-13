<?php

namespace App\Modules\Demo\Models;

use Nova\ORM\Model as BaseModel;


class User extends BaseModel
{
    protected $tableName = 'users';


    public function __construct()
    {
        parent::__construct();
    }


}
