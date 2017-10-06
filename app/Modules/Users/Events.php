<?php
/**
 * Events - all Module's specific Events are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use App\Models\Role;
use App\Models\User;


/** Define Events. */

Event::listen('backend.menu', function ($user)
{
    $items = array();

    if ($user->can('lists', User::class)) {
        $items[] = array(
            'path'   => 'users',
            'url'    => site_url('admin/users'),
            'title'  => __d('users', 'Users'),
            'icon'   => 'users',
            'weight' => 1,
        );
    }

    if ($user->can('lists', Role::class)) {
        $items[] = array(
            'path'   => 'platform.roles',
            'url'    => site_url('admin/roles'),
            'title'  => __d('users', 'User Roles'),
            'icon'   => 'book',
            'weight' => 2,
        );
    }

    return $items;
});
