<?php
/**
 * Events - all Module's specific Events are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */


/** Define Events. */

Event::listen('backend.menu', function($user)
{
    if (! $user->hasRole('administrator')) {
        return array();
    }

    $items = array(
        array(
            'uri'    => 'admin/settings',
            'title'  => __d('system', 'Settings'),
            'label'  => '',
            'icon'   => 'gears',
            'weight' => 0,
        ),
    );

    return $items;
});
