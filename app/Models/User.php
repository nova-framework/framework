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

    protected $fillable = array('role_id', 'username', 'password', 'realname', 'email', 'activated', 'image', 'activation_code');

    protected $hidden = array('password', 'activation_code', 'remember_token');

    public $files = array(
        'image' => array(
            'path'        => ROOTDIR .'assets/images/users/:unique_id-:file_name',
            'defaultPath' => ROOTDIR .'assets/images/users/no-image.png',
        ),
    );

    // Cache for associated Role instance.
    private $cachedRole;


    public function role()
    {
        return $this->hasOne('App\Models\Role', 'id', 'role_id');
    }

    public function hasRole($roles)
    {
        if (! isset($this->cachedRole)) {
            $this->cachedRole = $this->role()->getResults();
        }

        // Check if the User is a Root account.
        if (! is_null($this->cachedRole) && ($this->cachedRole->slug == 'root')) {
            return true;
        }

        if (! is_array($roles)) {
            return $this->checkUserRole($roles);
        }

        foreach ($roles as $role) {
            if ($this->checkUserRole($role)) {
                return true;
            }
        }

        return false;
    }

    private function checkUserRole($wantedRole)
    {
        if(isset($this->cachedRole) && ($this->cachedRole instanceof Role)) {
            return (strtolower($wantedRole) == strtolower($this->cachedRole->slug));
        }

        return false;
    }

}
