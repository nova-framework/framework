<?php

namespace Modules\Contacts\Policies;

use Modules\Contacts\Models\Message;
use Modules\Users\Models\User;

use Nova\Auth\Access\HandlesAuthorizationTrait;


class MessagePolicy
{
    use HandlesAuthorizationTrait;


    /**
     * Determine whether the user can view the messages list.
     *
     * @param  \Modules\Users\Models\User  $authUser
     * @return mixed
     */
    public function lists(User $authUser)
    {
        return $authUser->hasPermission('module.contacts.messages.lists');
    }

    /**
     * Determine whether the user can view the role.
     *
     * @param  \Modules\Users\Models\User  $authUser
     * @param  \Modules\Contacts\Models\Message  $message
     * @return mixed
     */
    public function view(User $authUser, Message $message)
    {
        return $authUser->hasPermission('module.contacts.messages.view');
    }

    /**
     * Determine whether the user can create messages.
     *
     * @param  \Modules\Users\Models\User  $authUser
     * @return mixed
     */
    public function create(User $authUser)
    {
        if (is_null($authUser)) {
            // The Guests are always allowed to create Contact Messages.
            return true;
        }

        return $authUser->hasPermission('module.contacts.messages.create');
    }

    /**
     * Determine whether the user can update the role.
     *
     * @param  \Modules\Users\Models\User  $authUser
     * @param  \Modules\Contacts\Models\Message  $message
     * @return mixed
     */
    public function update(User $authUser, Message $message)
    {
        return $authUser->hasPermission('module.contacts.messages.update');
    }

    /**
     * Determine whether the user can delete the role.
     *
     * @param  \Modules\Users\Models\User  $authUser
     * @param  \Modules\Contacts\Models\Message  $message
     * @return mixed
     */
    public function delete(User $authUser, Message $message)
    {
        return $authUser->hasPermission('module.contacts.messages.delete');
    }
}
