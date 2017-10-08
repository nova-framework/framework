<?php

namespace App\Policies;

use App\Models\User;

use Nova\Auth\Access\HandlesAuthorizationTrait;


class UserPolicy
{
    use HandlesAuthorizationTrait;


    /**
     * Determine whether the user can view the users list.
     *
     * @param  \App\Models\User  $authUser
     * @return mixed
     */
    public function lists(User $authUser)
    {
        return $authUser->hasPermission('app.users.lists');
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
        if ($authUser->hasPermission('app.users.view')) {
            return true;
        } else if ($authUser->id === $user->id) {
            return $authUser->hasPermission('app.users.view.own');
        }

        return false;
    }

    /**
     * Determine whether the user can create users.
     *
     * @param  \App\Models\User  $authUser
     * @return mixed
     */
    public function create(User $authUser)
    {
        return $authUser->hasPermission('app.users.create');
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
        if ($authUser->hasPermission('app.users.update')) {
            return true;
        } else if ($authUser->id === $user->id) {
            return $authUser->hasPermission('app.users.update.own');
        }

        return false;
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
        if ($authUser->hasPermission('app.users.delete')) {
            return true;
        } else if ($authUser->id === $user->id) {
            return $authUser->hasPermission('app.users.delete.own');
        }

        return false;
    }
}
