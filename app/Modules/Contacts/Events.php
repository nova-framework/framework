<?php

/*
|--------------------------------------------------------------------------
| Module Events
|--------------------------------------------------------------------------
|
| Here is where you can register all of the Events for the module.
*/


/**
 * Handle the Backend Menu Sidebar.
 */
Event::listen('backend.menu.sidebar', function ()
{
    return array(
        array(
            'url'    => site_url('admin/contacts'),
            'title'  => __d('contacts', 'Contacts'),
            'icon'   => 'address-book-o',
            'weight' => 4,

            //
            'path'   => 'contacts',
        ),
    );
});
