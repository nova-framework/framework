<?php

namespace App\Modules\Users\Models;

use Nova\Database\ORM\Model as BaseModel;


class Role extends BaseModel
{
    protected $table = 'roles';

    protected $primaryKey = 'id';

    protected $fillable = array('name', 'slug', 'description');


    public function users()
    {
        return $this->hasMany('App\Modules\Users\Models\User', 'role_id');
    }

}
