<?php
/**
 * Events - all Module's specific Events are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/** Define Events. */

Event::listen('backend.menu', function ()
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
            'role'   => 'administrator',
        ),
    );
});
