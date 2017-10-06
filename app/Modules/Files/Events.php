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
    if (! $user->hasRole('administrator')) {
        return array();
    }

    $items = array(
        array(
            'path'   => 'files',
            'url'    => site_url('admin/files'),
            'title'  => __d('files', 'Files'),
            'icon'   => 'file',
            'weight' => 3,
        ),
    );

    return $items;
});
