<?php

namespace App\Modules\Demo\Models;

use Nova\ORM\Model as BaseModel;


class Member extends BaseModel
{
    protected $tableName = 'members';


    public function __construct()
    {
        parent::__construct();
    }


}
