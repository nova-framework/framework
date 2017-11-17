<?php


return array(

    /**
    * The Widgets options.
    */
    'widgets' => array(
        'contentArchives' => array(
            // The mode how the paths are matched: 'show' or 'hide'
            'mode'    => 'show',

            // The request paths on which the Widget is visible.
            'paths'   => array('*'),

            // Authentication filters applied: 'guest' or 'auth'
            'filters' => array(
                // 'guest',
            ),
        ),
    ),

);
