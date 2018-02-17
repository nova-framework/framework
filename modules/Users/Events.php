<?php
/**
 * Events - all Module's specific Events are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Modules\Users\Models\User;


/** Define Events. */

Event::listen('backend.menu.sidebar', function ()
{
    return array(

        // Users.
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
    );
});
