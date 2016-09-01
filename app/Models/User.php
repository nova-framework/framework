<?php
/**
 * Users - A Users Model.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Models;

use Auth\UserInterface;
use Auth\Reminders\RemindableInterface;
use Database\ORM\Model as BaseModel;


class User extends BaseModel implements UserInterface, RemindableInterface
{
    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $fillable = array('role_id', 'username', 'password', 'realname', 'email', 'active', 'activation_code');

    protected $hidden = array('password', 'activation_code', 'remember_token');

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

    /**
     * Get the unique identifier for the User.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the User.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }

}
