<?php
/**
 * Events - all Module's specific Events are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/** Define Events. */

Event::listen('backend.menu.sidebar', function ()
{
    return array(
        array(
            'url'    => site_url('admin/files'),
            'title'  => __d('files', 'Files'),
            'icon'   => 'file',
            'weight' => 3,

            //
            'path'   => 'files',
            'role'   => 'administrator',
        ),
    );
});
