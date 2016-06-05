<?php

namespace App\Models;

use Database\ORM\Model as BaseModel;


class Role extends BaseModel
{
    protected $table = 'roles';

    public $timestamps = true;


    public function users()
    {
        return $this->hasMany('App\Models\User', 'role_id');
    }

}
