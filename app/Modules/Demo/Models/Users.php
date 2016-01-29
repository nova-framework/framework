<?php

namespace App\Modules\Demo\Models;

use App\Core\BaseModel;


class Users extends BaseModel
{
    protected $table = 'users';


    public function __construct()
    {
        parent::__construct();
    }


}
