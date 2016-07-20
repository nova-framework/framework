<?php
/**
 * RoleVerifier - A Roles Verifier for being used together with the Database Auth Driver.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Modules\Users\Helpers;

use App\Modules\Users\Models\Roles;

use Auth;
use DB;


class RoleVerifier
{
    protected $model;

    protected $cachedRole;

    protected static $instance;


    protected function __construct()
    {
        $this->model = new Roles();
    }

    protected static function getInstance()
    {
        if(! isset(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public static function userHasRole($roles)
    {
        $instance = static::getInstance();

        // Get the User instance.
        $user = Auth::user();

        // Get the associated Role information.
        $role = $instance->getGenericUserRole($user);

        // Check if the User is a Root account.
        if (is_null($role)) return false;

        // Check if the User is a Root account.
        if ($role->slug == 'root') return true;

        if (! is_array($roles)) return static::checkUserRole($roles);

        foreach ($roles as $wantedRole) {
            if (static::checkUserRole($role, $wantedRole)) {
                return true;
            }
        }

        return false;
    }

    protected static function checkUserRole($role, $wantedRole)
    {
        return (strtolower($wantedRole) == strtolower($role->slug));
    }

    protected function getGenericUserRole(GenericUser $user)
    {
        if (isset($this->cachedRole)) return $this->cachedRole;

        return $this->cachedRole = $this->model->find($user->role_id);
    }

}
