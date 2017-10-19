<?php

namespace App\Modules\Roles\Models;

use Nova\Database\ORM\Model as BaseModel;


class Role extends BaseModel
{
    protected $table = 'roles';

    protected $primaryKey = 'id';

    protected $fillable = array('name', 'slug', 'description');


    public function users()
    {
        return $this->belongsToMany('App\Modules\Users\Models\User', 'role_user', 'role_id', 'user_id');
    }

    public function permissions()
    {
        return $this->belongsToMany('App\Modules\Permissions\Models\Permission', 'permission_role', 'role_id', 'permission_id');
    }

}
