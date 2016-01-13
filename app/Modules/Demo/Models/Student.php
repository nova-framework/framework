<?php

namespace App\Modules\Demo\Models;

use Nova\ORM\Model as BaseModel;


class Student extends BaseModel
{
    protected $tableName = 'students';


    public function __construct()
    {
        parent::__construct();
    }


}
