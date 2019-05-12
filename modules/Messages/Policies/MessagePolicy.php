<?php

namespace Modules\Messages\Policies;

use Modules\Users\Models\User;

use Nova\Auth\Access\HandlesAuthorizationTrait;


class MessagePolicy
{
    use HandlesAuthorizationTrait;


    /**
     * Determine whether the user can view the messages list.
     *
     * @param  \App\Moudules\Users\Models\User  $authUser
     * @return mixed
     */
    public function lists(User $authUser)
    {
        return $authUser->hasPermission('module.messages.lists');
    }

    /**
     * Determine whether the user can view messages.
     *
     * @param  \App\Moudules\Users\Models\User  $authUser
     * @return mixed
     */
    public function view(User $authUser)
    {
        return $authUser->hasPermission('module.messages.view');
    }

    /**
     * Determine whether the user can create messages.
     *
     * @param  \App\Moudules\Users\Models\User  $authUser
     * @return mixed
     */
    public function create(User $authUser)
    {
        return $authUser->hasPermission('module.messages.create');
    }
}
