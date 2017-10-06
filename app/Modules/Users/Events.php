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

Event::listen('backend.menu', function ()
{
    return array(
        array(
            'path'   => 'platform',
            'url'    => '#',
            'title'  => __d('users', 'Platform'),
            'icon'   => 'cube',
            'weight' => 0,
        ),
        array(
            'path'   => 'users',
            'url'    => '#',
            'title'  => __d('users', 'Users'),
            'icon'   => 'users',
            'weight' => 0,
        ),
        array(
            'path'   => 'users.list',
            'can'    => 'lists:' .User::class,
            'url'    => site_url('admin/users'),
            'title'  => __d('users', 'Users List'),
            'icon'   => 'circle-o',
            'weight' => 0,
        ),
        array(
            'path'   => 'users.create',
            'can'    => 'create:' .User::class,
            'url'    => site_url('admin/users/create'),
            'title'  => __d('users', 'Create a new User'),
            'icon'   => 'circle-o',
            'weight' => 1,
        ),
        array(
            'path'   => 'platform.roles',
            'can'    => 'lists:' .Role::class,
            'url'    => site_url('admin/roles'),
            'title'  => __d('users', 'User Roles'),
            'icon'   => 'circle-o',
            'weight' => 1,
        ),
    );
});
