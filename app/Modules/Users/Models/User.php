<?php

namespace App\Modules\Users\Models;

use Nova\Auth\UserTrait;
use Nova\Auth\UserInterface;
use Nova\Database\ORM\Model as BaseModel;
use Nova\Foundation\Auth\Access\AuthorizableTrait;
use Nova\Support\Facades\Cache;

use Shared\Auth\Reminders\RemindableTrait;
use Shared\Auth\Reminders\RemindableInterface;
use Shared\Database\ORM\FileField\FileFieldTrait;
use Shared\Notifications\NotifiableTrait;

use App\Modules\Attachments\Traits\HasAttachmentsTrait;
use App\Modules\Fields\Traits\MetableTrait;
use App\Modules\Messages\Traits\HasMessagesTrait;
use App\Modules\Platform\Traits\HasActivitiesTrait;


class User extends BaseModel implements UserInterface, RemindableInterface
{
    use UserTrait, RemindableTrait, AuthorizableTrait, MetableTrait, NotifiableTrait, FileFieldTrait, HasActivitiesTrait, HasMessagesTrait, HasAttachmentsTrait;

    //
    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $fillable = array('username', 'password', 'email', 'image', 'profile_id');

    protected $hidden = array('password', 'remember_token');

    public $files = array(
        'image' => array(
            'path'        => ROOTDIR .'assets/images/users/:unique_id-:file_name',
            'defaultPath' => ROOTDIR .'assets/images/users/no-image.png',
        ),
    );

    // Setup the Metadata.
    protected $with = array('meta');

    protected $metaTable = 'users_meta';

    // Caches for Roles and Permissions.
    protected $cachedRoles;
    protected $cachedPermissions;


    public function profile()
    {
        return $this->belongsTo('App\Modules\Users\Models\Profile', 'profile_id');
    }

    public function realname()
    {
        return trim($this->meta->first_name .' ' .$this->meta->last_name);
    }

    public function picture()
    {
        $path = 'assets/files/pictures/';

        $picture = $this->meta->picture;

        if (! empty($picture) && is_readable(ROOTDIR .($path .= basename($picture)))) {
            // Nothing to do.
        } else {
            // Fallback to the default image.
            $path = 'assets/images/users/no-image.png';
        }

        return site_url($path);
    }

    public function getMetaFields()
    {
        return $this->profile->fields->getMetaFields($this->meta);
    }

    /**
     * Roles and Permissions (ACL)
     */

    public function roles()
    {
        return $this->belongsToMany('App\Modules\Roles\Models\Role', 'role_user', 'user_id', 'role_id');
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
