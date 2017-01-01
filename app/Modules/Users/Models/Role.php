<?php

namespace App\Modules\Users\Models;

use Nova\Database\ORM\Model as BaseModel;

use App\Modules\Logs\Observers\UserActionsObserver;


class Role extends BaseModel
{
    protected $table = 'roles';

    protected $primaryKey = 'id';

    protected $fillable = array('name', 'slug', 'description');


    public static function boot()
    {
        parent::boot();

        static::observe(new UserActionsObserver());
    }

    public function users()
    {
        return $this->hasMany('App\Modules\Users\Models\User', 'role_id');
    }

}
