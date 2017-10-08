<?php

namespace App\Models;

use Nova\Auth\UserTrait;
use Nova\Auth\UserInterface;
use Nova\Auth\Reminders\RemindableTrait;
use Nova\Auth\Reminders\RemindableInterface;
use Nova\Database\ORM\Model as BaseModel;
use Nova\Foundation\Auth\Access\AuthorizableTrait;
use Nova\Support\Facades\Cache;

use Shared\Database\ORM\FileField\FileFieldTrait;


class User extends BaseModel implements UserInterface, RemindableInterface
{
    use UserTrait, RemindableTrait, AuthorizableTrait, FileFieldTrait;

    //
    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $fillable = array('role_id', 'username', 'password', 'realname', 'email', 'activated', 'image', 'activation_code', 'api_token');

    protected $hidden = array('password', 'activation_code', 'remember_token', 'api_token');

    public $files = array(
        'image' => array(
            'path'        => ROOTDIR .'assets/images/users/:unique_id-:file_name',
            'defaultPath' => ROOTDIR .'assets/images/users/no-image.png',
        ),
    );

    // Cache for the slugs of permissions inherited from the associated role(s)
    protected $permissions;


    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'role_user', 'user_id', 'role_id');
    }

    public function hasRole($role, $strict = false)
    {
        $roles = Cache::remember('user.roles.' .$this->getKey(), 1440, function ()
        {
            return $this->roles->lists('slug');
        });

        if (in_array('root', $roles) && ! $strict) {
            // The ROOT is allowed for all permissions.
            return true;
        }

        return (bool) count(array_intersect($roles, (array) $role));
    }

    public function hasPermission($permission)
    {
        $permissions = is_array($permission) ? $permission : func_get_args();

        if (in_array('root', $this->roles->lists('slug'))) {
            // The ROOT is allowed for all permissions.
            return true;
        }

        return (bool) count(array_intersect($permissions, $this->getPermissions()));
    }

    protected function getPermissions()
    {
        if (isset($this->permissions)) {
            return $this->permissions;
        }

        return $this->permissions = Cache::remember('user.permissions.' .$this->getKey(), 1440, function ()
        {
            return $this->roles->load('permissions')->pluck('permissions')->lists('slug');
        });
    }
}
