<?php


return array(

    /**
    * The Widgets options.
    */
    'widgets' => array(
        'contentArchives' => array(
            // The status: 'publish' to show it, anything else to hide it.
            'status' => 'publish',

            // The request paths on which the Widget is shown or hidden.
            'path'   => array('*'),

            // The mode how the matched paths are handled: 'show' or 'hide'
            'mode'   => 'show',

            // The authentication filter applied: 'any', 'user' or 'guest'
            'filter' => 'any',
        ),
    ),

);
