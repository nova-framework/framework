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
    $items = array();

    if ($user->can('index', 'App\Models\User')) {
        $items[] = array(
            'uri'    => 'admin/users',
            'title'  => __d('users', 'Users'),
            'icon'   => 'users',
            'weight' => 1,
        );
    }

    if ($user->hasRole('administrator')) {
        $items[] = array(
            'uri'    => 'admin/roles',
            'title'  => __d('users', 'Roles'),
            'icon'   => 'book',
            'weight' => 2,
        );
    }

    return $items;
});
