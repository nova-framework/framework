<?php
/**
 * Events - all Module's specific Events are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use App\Models\Option;


/** Define Events. */

Event::listen('frontend.menu.left', function ()
{
    return array(
        array(
            'url'    => site_url('dashboard'),
            'title'  => __d('system', 'Dashboard'),
            'icon'   => 'dashboard',
            'weight' => 0,

            //
            'path'   => 'dashboard',
        ),
    );
});

Event::listen('frontend.menu.right', function ()
{
    return array(
        array(
            'url'    => '#',
            'title'  => '',
            'icon'   => 'bell-o',
            'weight' => 1,

            //
            'path'   => 'notifications',

            // Custom content
            'class'   => 'notifications-menu',
            'content' => View::fetch('Partials/NavbarNotifications', array(), 'System'),
        ),
        array(
            'url'    => site_url('admin/dashboard'),
            'title'  => __d('system', 'Administration'),
            'icon'   => 'server',
            'weight' => 101,

            //
            'path'   => 'backend',
            'can'    => 'platform.backend.manage'
        ),
    );
});

Event::listen('backend.menu.sidebar', function ()
{
    return array(
        array(
            'url'    => site_url('admin/dashboard'),
            'title'  => __d('system', 'Dashboard'),
            'icon'   => 'dashboard',
            'weight' => 0,

            //
            'path'   => 'dashboard',
        ),
        array(
            'url'    => '#',
            'title'  => __d('system', 'Platform'),
            'icon'   => 'cube',
            'weight' => 0,

            //
            'path'   => 'platform',
        ),
        array(
            'url'    => site_url('admin/settings'),
            'title'  => __d('system', 'Settings'),
            'icon'   => 'circle-o',
            'weight' => 0,

            //
            'path'   => 'platform.settings',
            'can'    => 'manage:' .Option::class,
        ),
    );
});


Event::listen('backend.menu.navbar', function ()
{
    return array(
        array(
            'url'    => '#',
            'title'  => '',
            'icon'   => 'bell-o',
            'weight' => 1,

            //
            'path'   => 'notifications',

            // Custom content
            'class'   => 'notifications-menu',
            'content' => View::fetch('Partials/NavbarNotifications', array(), 'System'),
        ),
        array(
            'url'    => site_url('dashboard'),
            'title'  => __d('system', 'Frontend'),
            'icon'   => 'home',
            'weight' => 101,

            //
            'path'   => 'frontend',
            'can'    => 'platform.backend.manage'
        ),
    );
});
