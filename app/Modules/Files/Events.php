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
    $items = array(
        array(
            'path'   => 'files',
            'role'   => 'administrator',
            'url'    => site_url('admin/files'),
            'title'  => __d('files', 'Files'),
            'icon'   => 'file',
            'weight' => 3,
        ),
    );

    return $items;
});
