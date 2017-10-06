<?php
/**
 * Events - all Module's specific Events are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/** Define Events. */

Event::listen('backend.menu', function ($user)
{
    $items = array(
        array(
            'path'   => 'dashboard',
            'url'    => site_url('admin/dashboard'),
            'title'  => __d('system', 'Dashboard'),
            'icon'   => 'dashboard',
            'weight' => 0,
        )
    );

    if (! $user->hasRole('administrator')) {
        return $items;
    }

    $items[] = array(
        'path'   => 'platform',
        'url'    => '#',
        'title'  => __d('system', 'Platform'),
        'icon'   => 'cube',
        'weight' => 0,
    );

    $items[] = array(
        'path'   => 'platform.settings',
        'url'    => site_url('admin/settings'),
        'title'  => __d('system', 'Settings'),
        'icon'   => 'circle-o',
        'weight' => 0,
    );

    return $items;
});
