<?php

namespace Modules\Users\Policies;

use Modules\Users\Models\FieldItem;
use Modules\Users\Models\User;

use Nova\Auth\Access\HandlesAuthorizationTrait;


class FieldItemPolicy
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
        return $authUser->hasPermission('module.users.fields.lists');
    }

    /**
     * Determine whether the user can view the role.
     *
     * @param  \App\Models\User  $authUser
     * @param  \App\Models\FieldItem  $item
     * @return mixed
     */
    public function view(User $authUser, FieldItem $item)
    {
        return $authUser->hasPermission('module.users.fields.view');
    }

    /**
     * Determine whether the user can create roles.
     *
     * @param  \App\Models\User  $authUser
     * @return mixed
     */
    public function create(User $authUser)
    {
        return $authUser->hasPermission('module.users.fields.create');
    }

    /**
     * Determine whether the user can update the role.
     *
     * @param  \App\Models\User  $authUser
     * @param  \App\Models\FieldItem  $item
     * @return mixed
     */
    public function update(User $authUser, FieldItem $item)
    {
        return $authUser->hasPermission('module.users.fields.update');
    }

    /**
     * Determine whether the user can delete the role.
     *
     * @param  \App\Models\User  $authUser
     * @param  \App\Models\FieldItem  $item
     * @return mixed
     */
    public function delete(User $authUser, FieldItem $item)
    {
        return $authUser->hasPermission('module.users.fields.delete');
    }
}
