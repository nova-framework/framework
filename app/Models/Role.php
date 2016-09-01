<?php

namespace App\Models;

use Database\ORM\Model as BaseModel;


class Role extends BaseModel
{
    protected $table = 'roles';

    protected $primaryKey = 'id';

    protected $fillable = array('name', 'slug', 'description');


    public function users()
    {
        return $this->hasMany('App\Models\User', 'role_id');
    }

}
