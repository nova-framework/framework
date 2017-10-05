<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Role;

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
        return $this->authorize($authUser);
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
        return $this->authorize($authUser);
    }

    /**
     * Determine whether the user can create roles.
     *
     * @param  \App\Models\User  $authUser
     * @return mixed
     */
    public function create(User $authUser)
    {
        return $this->authorize($authUser);
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
        return $this->authorize($authUser);
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
        return $this->authorize($authUser);
    }

    /**
     * Determine whether the user is an administrator.
     *
     * @param  \App\Models\User  $authUser
     * @return bool
     */
    protected function authorize(User $authUser)
    {
        return $authUser->hasRole('administrator');
    }
}
