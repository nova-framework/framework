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
use Shared\Notifications\NotifiableTrait;


class User extends BaseModel implements UserInterface, RemindableInterface
{
    use UserTrait, RemindableTrait, AuthorizableTrait, NotifiableTrait, FileFieldTrait;

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

    // Caches for Roles and Permissions.
    protected $cachedRoles;
    protected $cachedPermissions;


    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'role_user', 'user_id', 'role_id');
    }

    public function hasRole($role, $strict = false)
    {
        if (in_array('root', $roles = $this->getCachedRoles()) && ! $strict) {
            // The ROOT can impersonate any Role.
            return true;
        }

        return (bool) count(array_intersect($roles, (array) $role));
    }

    public function hasPermission($permission)
    {
        $permissions = is_array($permission) ? $permission : func_get_args();

        if (($this->getKey() === 1) || in_array('root', $this->getCachedRoles())) {
            // The USER ONE and all ROOT users are allowed for all permissions.
            return true;
        }

        return (bool) count(array_intersect($permissions, $this->getCachedPermissions()));
    }

    protected function getCachedRoles()
    {
        if (isset($this->cachedRoles)) {
            return $this->cachedRoles;
        }

        $cacheKey = 'user.roles.' .$this->getKey();

        return $this->cachedRoles = Cache::remember($cacheKey, 1440, function ()
        {
            return $this->roles->lists('slug');
        });
    }

    protected function getCachedPermissions()
    {
        if (isset($this->cachedPermissions)) {
            return $this->cachedPermissions;
        }

        $cacheKey = 'user.permissions.' .$this->getKey();

        return $this->cachedPermissions = Cache::remember($cacheKey, 1440, function ()
        {
            return $this->roles->load('permissions')->pluck('permissions')->lists('slug');
        });
    }
}
