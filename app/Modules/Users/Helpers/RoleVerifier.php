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
    protected static $cachedRole;


    public static function userHasRole($roles)
    {
        if (! isset(static::$cachedRole)) {
            $user = Auth::user();

            static::$cachedRole = $role = static::getGenericUserRole($user);
        } else {
            $role = static::$cachedRole;
        }

        // Check if the User is a Root account.
        if (is_null($role)) return false;

        // Check if the User is a Root account.
        if ($role->slug == 'root') return true;

        if (! is_array($roles)) return static::checkUserRole($roles);

        foreach ($roles as $wantedRole) {
            if (static::checkUserRole($wantedRole)) {
                return true;
            }
        }

        return false;
    }

    protected function checkUserRole($wantedRole)
    {
        if(! isset(static::$cachedRole)) {
            return false;
        }

        $role = static::$cachedRole;

        return (strtolower($wantedRole) == strtolower($role->slug));
    }

    protected static function getGenericUserRole(GenericUser $user)
    {
        // Retrieve the data from the User Model instance.
        $roleId = $user->role_id;

        $table = Roles::getTableName();

        return DB::table($table)->find($roleId);
    }

}
