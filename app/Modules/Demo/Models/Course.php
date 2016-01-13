<?php

namespace App\Modules\Demo\Models;

use Nova\ORM\Model as BaseModel;


class Course extends BaseModel
{
    protected $tableName = 'courses';


    public function __construct()
    {
        parent::__construct();
    }


}
