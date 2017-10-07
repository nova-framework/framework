<?php

namespace App\Models;

use Nova\Database\ORM\Model as BaseModel;


class Permission extends BaseModel
{
    protected $table = 'permissions';

    protected $primaryKey = 'id';

    protected $fillable = array('name', 'slug', 'group');


    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'permission_role', 'permission_id', 'role_id');
    }
}
