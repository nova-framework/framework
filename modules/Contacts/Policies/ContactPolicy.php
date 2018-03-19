<?php

namespace Modules\Contacts\Policies;

use Modules\Contacts\Models\Contact;
use Modules\Users\Models\User;

use Nova\Auth\Access\HandlesAuthorizationTrait;


class ContactPolicy
{
    use HandlesAuthorizationTrait;


    /**
     * Determine whether the user can view the contacts list.
     *
     * @param  \Modules\Users\Models\User  $authUser
     * @return mixed
     */
    public function lists(User $authUser)
    {
        return $authUser->hasPermission('module.contacts.contacts.lists');
    }

    /**
     * Determine whether the user can view the role.
     *
     * @param  \Modules\Users\Models\User  $authUser
     * @param  \Modules\Contacts\Models\Contact  $contact
     * @return mixed
     */
    public function view(User $authUser, Contact $contact)
    {
        return $authUser->hasPermission('module.contacts.contacts.view');
    }

    /**
     * Determine whether the user can create contacts.
     *
     * @param  \Modules\Users\Models\User  $authUser
     * @return mixed
     */
    public function create(User $authUser)
    {
        return $authUser->hasPermission('module.contacts.contacts.create');
    }

    /**
     * Determine whether the user can update the role.
     *
     * @param  \Modules\Users\Models\User  $authUser
     * @param  \Modules\Contacts\Models\Contact  $contact
     * @return mixed
     */
    public function update(User $authUser, Contact $contact)
    {
        return $authUser->hasPermission('module.contacts.contacts.update');
    }

    /**
     * Determine whether the user can delete the role.
     *
     * @param  \Modules\Users\Models\User  $authUser
     * @param  \Modules\Contacts\Models\Contact  $contact
     * @return mixed
     */
    public function delete(User $authUser, Contact $contact)
    {
        return $authUser->hasPermission('module.contacts.contacts.delete');
    }
}
