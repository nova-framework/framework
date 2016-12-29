<?php

/*
|--------------------------------------------------------------------------
| Module Events
|--------------------------------------------------------------------------
|
| Here is where you can register all of the Events for the module.
*/

Event::listen('backend.menu', function($user) {
    $label = View::make('Partials/UnreadCount', array(), 'Messenger')->render();

    $items = array(
        array(
            'uri'    => 'admin/messages',
            'title'  => __d('messenger', 'Messages'),
            'label'  => $label,
            'icon'   => 'wechat',
            'weight' => 2,
        ),
    );

    return $items;
});
