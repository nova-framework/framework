<?php

namespace App\Modules\Demo\Models;

use App\Core\ClassicModel as BaseModel;


class ClassicMembers extends BaseModel
{
    protected $table = 'members';


    public function __construct()
    {
        parent::__construct();
    }


}
