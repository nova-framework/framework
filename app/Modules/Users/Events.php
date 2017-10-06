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
    $items = array(
        array(
            'path'   => 'users',
            'url'    => '#',
            'title'  => __d('system', 'Users'),
            'icon'   => 'users',
            'weight' => 0,
        )
    );

    if ($user->can('lists', User::class)) {
        $items[] = array(
            'path'   => 'users.list',
            'url'    => site_url('admin/users'),
            'title'  => __d('users', 'Users List'),
            'icon'   => 'circle-o',
            'weight' => 0,
        );
    }

    if ($user->can('create', User::class)) {
        $items[] = array(
            'path'   => 'users.create',
            'url'    => site_url('admin/users/create'),
            'title'  => __d('users', 'Create a new User'),
            'icon'   => 'circle-o',
            'weight' => 1,
        );
    }

    if ($user->can('lists', Role::class)) {
        $items[] = array(
            'path'   => 'platform.roles',
            'url'    => site_url('admin/roles'),
            'title'  => __d('users', 'User Roles'),
            'icon'   => 'circle-o',
            'weight' => 1,
        );
    }

    return $items;
});
