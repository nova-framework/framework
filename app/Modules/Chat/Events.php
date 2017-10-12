<?php

/*
|--------------------------------------------------------------------------
| Module Events
|--------------------------------------------------------------------------
|
| Here is where you can register all of the Events for the module.
*/

Event::listen('frontend.menu.left', function ()
{
    return array(
        array(
            'url'    => site_url('chat'),
            'title'  => __d('system', 'Chat'),
            'icon'   => 'comments',
            'weight' => 1,

            //
            'path'   => 'chat',
        ),
    );
});


