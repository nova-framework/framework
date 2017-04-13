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
            'title'  => __d('users', 'Users'),
            'icon'   => 'users',
            'weight' => 1,
            'children' => array(
                array(
                    'uri'    => 'admin/users',
                    'title'  => __d('users', 'Users List'),
                    'label'  => '',
                    'weight' => 0,
                ),
                array(
                    'uri'    => 'admin/users/create',
                    'title'  => __d('users', 'Create a new User'),
                    'label'  => '',
                    'weight' => 1,
                ),
                array(
                    'uri'    => 'admin/roles',
                    'title'  => __d('users', 'User Roles'),
                    'label'  => '',
                    'weight' => 2,
                ),
                array(
                    'uri'    => 'admin/roles/create',
                    'title'  => __d('users', 'Create a new Role'),
                    'label'  => '',
                    'weight' => 3,
                ),
            ),
        ),
    );

    return $items;
});