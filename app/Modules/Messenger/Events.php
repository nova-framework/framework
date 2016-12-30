<?php

/*
|--------------------------------------------------------------------------
| Module Events
|--------------------------------------------------------------------------
|
| Here is where you can register all of the Events for the module.
*/

Event::listen('backend.menu', function($user)
{
    $user = Auth::user();

    // Prepare the Label.
    $data = array(
        'count' => $user->newMessagesCount()
    );

    $label = View::make('Partials/UnreadCount', $data, 'Messenger')->render();

    // Prepare the Items.
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
