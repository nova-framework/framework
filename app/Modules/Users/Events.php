<?php
/**
 * Events - all Module's specific Events are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use App\Modules\Users\Models\Profile;
use App\Modules\Users\Models\User;


/** Define Events. */

Event::listen('backend.menu.sidebar', function ()
{
    return array(

        // Profile.
        array(
            'url'    => '#',
            'title'  => __d('roles', 'Platform'),
            'icon'   => 'cube',
            'weight' => 0,

            //
            'path'   => 'platform',
        ),
        array(
            'url'    => site_url('admin/profile'),
            'title'  => __d('roles', 'Users Profile'),
            'icon'   => 'circle-o',
            'weight' => 2,

            //
            'path'   => 'platform.profile',
            'can'    => 'manage:' .Profile::class,
        ),

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
