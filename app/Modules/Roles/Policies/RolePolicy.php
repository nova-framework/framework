<?php

namespace App\Modules\Roles\Policies;

use App\Modules\Users\Models\User;
use App\Modules\Roles\Models\Role;

use Nova\Auth\Access\HandlesAuthorizationTrait;


class RolePolicy
{
    use HandlesAuthorizationTrait;


    /**
     * Determine whether the user can view the roles list.
     *
     * @param  \App\Models\User  $authUser
     * @return mixed
     */
    public function lists(User $authUser)
    {
        return $authUser->hasPermission('module.roles.lists');
    }

    /**
     * Determine whether the user can view the role.
     *
     * @param  \App\Models\User  $authUser
     * @param  \App\Models\Role  $role
     * @return mixed
     */
    public function view(User $authUser, Role $role)
    {
        return $authUser->hasPermission('module.roles.view');
    }

    /**
     * Determine whether the user can create roles.
     *
     * @param  \App\Models\User  $authUser
     * @return mixed
     */
    public function create(User $authUser)
    {
        return $authUser->hasPermission('module.roles.create');
    }

    /**
     * Determine whether the user can update the role.
     *
     * @param  \App\Models\User  $authUser
     * @param  \App\Models\Role  $role
     * @return mixed
     */
    public function update(User $authUser, Role $role)
    {
        return $authUser->hasPermission('module.roles.update');
    }

    /**
     * Determine whether the user can delete the role.
     *
     * @param  \App\Models\User  $authUser
     * @param  \App\Models\Role  $role
     * @return mixed
     */
    public function delete(User $authUser, Role $role)
    {
        return $authUser->hasPermission('module.roles.delete');
    }
}
