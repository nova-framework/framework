<?php
/**
 * Users - A Users Model.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Models;

use Nova\Auth\UserTrait;
use Nova\Auth\UserInterface;
use Nova\Auth\Reminders\RemindableTrait;
use Nova\Auth\Reminders\RemindableInterface;
use Nova\Database\ORM\Model as BaseModel;
use Nova\Foundation\Auth\Access\AuthorizableTrait;

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

    // For caching the permission slugs.
    protected $permissions;


    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'role_user', 'user_id', 'role_id');
    }

    public function hasRole($roles, $strict = false)
    {
        $slugs = $this->roles->lists('slug');

        if (in_array('root', $slugs) && ! $strict) {
            return true;
        }

        $roles = is_array($roles) ? $roles : array($roles);

        foreach ($roles as $role) {
            if (in_array(strtolower($role), $slugs)) {
                return true;
            }
        }

        return false;
    }

    public function hasPermission($permissions)
    {
        $permissions = is_array($permissions) ? $permissions : func_get_args();

        if (! isset($this->permissions)) {
            $this->permissions = $this->roles->load('permissions')->pluck('permissions')->lists('slug');
        }

        return (bool) count(array_intersect($this->permissions, $permissions));
    }
}
