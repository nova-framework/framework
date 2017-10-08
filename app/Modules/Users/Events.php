<?php
/**
 * Events - all Module's specific Events are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;


/** Define Events. */

Event::listen('backend.menu', function ()
{
    return array(
        array(
            'url'    => '#',
            'title'  => __d('users', 'Platform'),
            'icon'   => 'cube',
            'weight' => 0,

            //
            'path'   => 'platform',
        ),
        array(
            'url'    => '#',
            'title'  => __d('users', 'Users'),
            'icon'   => 'users',
            'weight' => 0,

            //
            'path'   => 'users',
        ),
        array(
            'url'    => site_url('admin/users'),
            'title'  => __d('users', 'Users List'),
            'icon'   => 'circle-o',
            'weight' => 0,

            //
            'path'   => 'users.list',
            'can'    => 'lists:' .User::class,
        ),
        array(
            'url'    => site_url('admin/users/create'),
            'title'  => __d('users', 'Create a new User'),
            'icon'   => 'circle-o',
            'weight' => 1,

            //
            'path'   => 'users.create',
            'can'    => 'create:' .User::class,
        ),
        array(
            'url'    => site_url('admin/roles'),
            'title'  => __d('users', 'User Roles'),
            'icon'   => 'circle-o',
            'weight' => 1,

            //
            'path'   => 'platform.roles',
            'can'    => 'lists:' .Role::class,
        ),
        array(
            'url'    => site_url('admin/permissions'),
            'title'  => __d('users', 'Permissions'),
            'icon'   => 'circle-o',
            'weight' => 1,

            //
            'path'   => 'platform.permissions',
            'can'    => 'manage:' .Permission::class,
        ),
    );
});
