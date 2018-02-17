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
            'url'    => '#',
            'title'  => __d('contacts', 'Contacts'),
            'icon'   => 'address-book-o',
            'weight' => 4,

            //
            'path'   => 'contacts',
        ),
        array(
            'url'    => site_url('admin/contacts'),
            'title'  => __d('contacts', 'All Contacts'),
            'icon'   => 'circle-o',
            'weight' => 0,

            //
            'path'   => 'contacts.list',
            //'can'    => 'lists:' .Contact::class,
        ),
        array(
            'url'    => site_url('admin/contacts/create'),
            'title'  => __d('contacts', 'Create a new Contact'),
            'icon'   => 'circle-o',
            'weight' => 1,

            //
            'path'   => 'contacts.create',
            //'can'    => 'create:' .Contact::class,
        ),
    );
});
