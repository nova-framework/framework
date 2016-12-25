<?php
/**
 * Events - all Module's specific Events are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/** Define Events. */

Event::listen('backend.menu', function($user) {
    if ($user->hasRole('administrator')) {
        $items = array(
            array(
                'uri'    => 'admin/users',
                'title'  => __d('users', 'Users'),
                'icon'   => 'users',
                'weight' => 1,
            ),
            array(
                'uri'    => 'admin/roles',
                'title'  => __d('users', 'Roles'),
                'icon'   => 'book',
                'weight' => 2,
            ),
        );
    } else {
        $items = array();
    }

    return $items;
});
