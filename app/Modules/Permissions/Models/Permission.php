<?php

namespace App\Modules\Permissions\Models;

use Nova\Database\ORM\Model as BaseModel;
use Nova\Database\QueryException;

use PDOException;


class Permission extends BaseModel
{
    protected $table = 'permissions';

    protected $primaryKey = 'id';

    protected $fillable = array('name', 'slug', 'group');


    public function roles()
    {
        return $this->belongsToMany('App\Modules\Roles\Models\Role', 'permission_role', 'permission_id', 'role_id');
    }

    public static function getResults()
    {
        $instance = new static;

        try {
            return $instance->newQuery()->get();
        }
        catch (QueryException $e) {
            //
        }
        catch (PDOException $e) {
            //
        }

        return $instance->newCollection();
    }
}
