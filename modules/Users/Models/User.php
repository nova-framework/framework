<?php

namespace Modules\Users\Models;

use Nova\Auth\UserTrait;
use Nova\Auth\UserInterface;
use Nova\Database\ORM\Model as BaseModel;
use Nova\Foundation\Auth\Access\AuthorizableTrait;
use Nova\Support\Facades\Cache;

use Shared\Auth\Reminders\RemindableTrait;
use Shared\Auth\Reminders\RemindableInterface;
use Shared\Database\ORM\MetaField\HasMetaFieldsTrait;
use Nova\Notification\NotifiableTrait;

use Modules\Messages\Traits\HasMessagesTrait;
use Modules\Platform\Traits\AliasesTrait;
use Modules\Platform\Traits\HasActivitiesTrait;


class User extends BaseModel implements UserInterface, RemindableInterface
{
    use UserTrait, RemindableTrait, AuthorizableTrait, AliasesTrait, HasMetaFieldsTrait, HasActivitiesTrait, HasMessagesTrait, NotifiableTrait;

    //
    protected $table = 'users';

    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = array('username', 'password', 'email', 'remember_token', 'api_token');

    /**
     * @var array
     */
    protected $hidden = array('password', 'remember_token');

    /**
     * @var array
     */
    protected $with = array('meta');

    /**
     * @var array
     */
    protected static $aliases = array(
        'first_name' => array('meta' => 'first_name'),
        'last_name'  => array('meta' => 'last_name'),
        'location'   => array('meta' => 'location'),
        'activated'  => array('meta' => 'activated'),
    );

    // ACL caches.
    protected $cachedRoles;
    protected $cachedPermissions;


    /**
     * @return \Nova\Database\ORM\Relations\HasMany
     */
    public function meta()
    {
        return $this->hasMany('Modules\Users\Models\UserMeta', 'user_id');
    }

    public function realname()
    {
        return trim($this->meta->first_name .' ' .$this->meta->last_name);
    }

    public function picture()
    {
        $picture = $this->meta->picture;

        $path = ! empty($picture) ? 'assets/files/pictures/' .basename($picture) : null;

        if (! is_null($path) && is_readable(BASEPATH .$path)) {
            // Nothing to do.
        } else {
            // Fallback to the default image.
            $path = 'assets/images/users/no-image.png';
        }

        return site_url($path);
    }

    /**
     * Roles and Permissions (ACL)
     */

    public function roles()
    {
        return $this->belongsToMany('Modules\Roles\Models\Role', 'role_user', 'user_id', 'role_id');
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
