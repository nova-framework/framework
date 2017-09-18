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


    public function role()
    {
        return $this->hasOne('App\Models\Role', 'id', 'role_id');
    }

    public function hasRole($roles)
    {
        if (! array_key_exists('role', $this->relations)) {
            $this->load('role');
        }

        $slug = strtolower($this->role->slug);

        // Check if the User is a Root account.
        if ($slug == 'root') {
            return true;
        }

        foreach ((array) $roles as $role) {
            if (strtolower($role) == $slug) {
                return true;
            }
        }

        return false;
    }
}
