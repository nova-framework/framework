<?php
/**
 * Events - all Module's specific Events are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use App\Modules\Platform\Models\Option;


Event::listen('frontend.menu.left', function ()
{
    return array(
        array(
            'url'    => site_url('dashboard'),
            'title'  => __d('platform', 'Dashboard'),
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
            'url'    => site_url('messages'),
            'title'  => __d('platform', 'Messages'),
            'icon'   => 'envelope-o',
            'weight' => 1,

            //
            'path'   => 'messages',

            // Custom content
            'class'   => 'messages-menu',
            'label'   => array('success', 0),
        ),
        array(
            'url'    => site_url('notifications'),
            'title'  => __d('platform', 'Notifications'),
            'icon'   => 'bell-o',
            'weight' => 1,

            //
            'path'   => 'notifications',

            // Custom content
            'class'   => 'notifications-menu',
            'label'   => array('warning', 0),
            'content' => View::fetch('Partials/NavbarNotifications', array(), 'Platform'),
        ),
        array(
            'url'    => site_url('admin/dashboard'),
            'title'  => __d('platform', 'Administration'),
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
            'title'  => __d('platform', 'Dashboard'),
            'icon'   => 'dashboard',
            'weight' => 0,

            //
            'path'   => 'dashboard',
        ),
        array(
            'url'    => '#',
            'title'  => __d('settings', 'Platform'),
            'icon'   => 'cube',
            'weight' => 0,

            //
            'path'   => 'platform',
        ),
        array(
            'url'    => site_url('admin/settings'),
            'title'  => __d('settings', 'Settings'),
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
            'url'    => site_url('messages'),
            'title'  => __d('platform', 'Messages'),
            'icon'   => 'envelope-o',
            'weight' => 1,

            //
            'path'   => 'messages',

            // Custom content
            'class'   => 'messages-menu',
            'label'   => array('success', 0),
        ),
        array(
            'url'    => site_url('notifications'),
            'title'  => __d('platform', 'Notifications'),
            'icon'   => 'bell-o',
            'weight' => 1,

            //
            'path'   => 'notifications',

            // Custom content
            'class'   => 'notifications-menu',
            'label'   => array('warning', 0),
            'content' => View::fetch('Partials/NavbarNotifications', array(), 'Platform'),
        ),
        array(
            'url'    => site_url('dashboard'),
            'title'  => __d('platform', 'Frontend'),
            'icon'   => 'home',
            'weight' => 101,

            //
            'path'   => 'frontend',
            'can'    => 'platform.backend.manage'
        ),
    );
});
