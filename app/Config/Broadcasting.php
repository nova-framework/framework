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

            'key'    => env('QUASAR_KEY'),
            'secret' => env('QUASAR_SECRET'),

            'options' => array(
                'authEndpoint' => site_url('broadcasting/auth'),

                // The HTTP server hostname and port.
                'httpHost'   => env('QUASAR_HTTP_HOST', '127.0.0.1'),
                'httpPort'   => env('QUASAR_HTTP_PORT', 2121),

                // The SocketIO server hostname and port.
                'socketHost' => env('QUASAR_SOCKET_HOST', '127.0.0.1'), // Optional, defaults to the HTTP host.
                'socketPort' => env('QUASAR_SOCKET_PORT', 2120),
            ),
        ),

        'pusher' => array(
            'driver'  => 'pusher',

            'key'     => env('PUSHER_KEY'),
            'secret'  => env('PUSHER_SECRET'),
            'app_id'  => env('PUSHER_APP_ID'),

            'options' => array(
                'authEndpoint' => site_url('broadcasting/auth'),
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
