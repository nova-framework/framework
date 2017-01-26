<?php
/**
 * Events - all Module's specific Events are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */


/** Define Events. */

Event::listen('backend.menu', function($user)
{
    $items = array(
        array(
            'title'  => __d('web_chat', 'Chat'),
            'icon'   => 'comments',
            'weight' => 9,
            'children' => array(
                array(
                    'uri'    => 'admin/chat',
                    'title'  => __d('web_chat', 'Web Chat'),
                    'label'  => '',
                    'weight' => 0,
                ),
                array(
                    'uri'    => 'admin/chat/video',
                    'title'  => __d('web_chat', 'Video Chat'),
                    'label'  => '',
                    'weight' => 1,
                ),
            ),
        ),
    );

    return $items;
});
