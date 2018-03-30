<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Default Broadcaster
    |--------------------------------------------------------------------------
    |
    | This option controls the default broadcaster that will be used by the
    | framework when an event needs to be broadcast. You may set this to
    | any of the connections defined in the "connections" array below.
    |
    */

    'default' => env('BROADCAST_DRIVER', 'log'),

    /*
    |--------------------------------------------------------------------------
    | Broadcast Connections
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the broadcast connections that will be used
    | to broadcast events to other systems or over websockets. Samples of
    | each available type of connection are provided inside this array.
    |
    */

    'connections' => array(

        'quasar' => array(
            'driver' => 'quasar',

            'appId'  => env('QUASAR_APP_ID'),
            'secret' => env('QUASAR_SECRET'),

            'host'   => env('QUASAR_HOST', '127.0.0.1'),
            'port'   => env('QUASAR_PORT', 2121),
        ),

        'pusher' => array(
            'driver'  => 'pusher',

            'key'     => env('PUSHER_KEY'),
            'secret'  => env('PUSHER_SECRET'),
            'app_id'  => env('PUSHER_APP_ID'),

            'options' => array(
                //
            ),
        ),

        'redis' => array(
            'driver'     => 'redis',
            'connection' => 'default',
        ),

        'log' => array(
            'driver' => 'log',
        ),

        'null' => array(
            'driver' => 'null',
        ),
    ),

);
