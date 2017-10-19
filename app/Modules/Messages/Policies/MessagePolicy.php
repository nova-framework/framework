<?php

namespace App\Modules\Platform\Policies;

use App\Modules\Users\Models\User;
use App\Models\Option;

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
     * Determine whether the user can send messages.
     *
     * @param  \App\Moudules\Users\Models\User  $authUser
     * @return mixed
     */
    public function send(User $authUser)
    {
        return $authUser->hasPermission('module.messages.send');
    }

    /**
     * Determine whether the user can receive messages.
     *
     * @param  \App\Moudules\Users\Models\User  $authUser
     * @return mixed
     */
    public function receive(User $authUser)
    {
        return $authUser->hasPermission('module.messages.receive');
    }
}
