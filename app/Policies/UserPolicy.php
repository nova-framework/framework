<?php

namespace App\Policies;

use App\Models\User;

use Nova\Auth\Access\HandlesAuthorizationTrait;


class UserPolicy
{
    use HandlesAuthorizationTrait;


    /**
     * Determine whether the user can view the users.
     *
     * @param  \App\Models\User  $authUser
     * @return mixed
     */
    public function index(User $authUser)
    {
        return true;
    }

    /**
     * Determine whether the user can view the user.
     *
     * @param  \App\Models\User  $authUser
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function view(User $authUser, User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can create users.
     *
     * @param  \App\Models\User  $authUser
     * @return mixed
     */
    public function create(User $authUser)
    {
        return $authUser->hasRole('administrator');
    }

    /**
     * Determine whether the user can update the user.
     *
     * @param  \App\Models\User  $authUser
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function update(User $authUser, User $user)
    {
        if ($authUser->id === $user->id) {
            return true;
        }

        return $authUser->hasRole('administrator');
    }

    /**
     * Determine whether the user can delete the user.
     *
     * @param  \App\Models\User  $authUser
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function delete(User $authUser, User $user)
    {
        return $authUser->hasRole('administrator');
    }
}
