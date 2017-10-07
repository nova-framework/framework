<?php

namespace App\Models;

use Nova\Database\ORM\Model as BaseModel;


class Role extends BaseModel
{
    protected $table = 'roles';

    protected $primaryKey = 'id';

    protected $fillable = array('name', 'slug', 'description');


    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'role_user', 'user_id', 'role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany('App\Models\Permission', 'permission_role', 'permission_id', 'role_id');
    }

}
