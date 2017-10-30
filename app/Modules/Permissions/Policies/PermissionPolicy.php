<?php

namespace App\Modules\Permissions\Policies;

use App\Modules\Users\Models\User;
use App\Models\Permission;

use Nova\Auth\Access\HandlesAuthorizationTrait;


class PermissionPolicy
{
    use HandlesAuthorizationTrait;


    /**
     * Determine whether the user can manage the permissions.
     *
     * @param  \App\Models\User  $authUser
     * @return mixed
     */
    public function manage(User $authUser)
    {
        return $authUser->hasPermission('module.permissions.manage');
    }
}
