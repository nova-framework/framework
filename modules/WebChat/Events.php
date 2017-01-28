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
            'title'  => __d('web_chat', 'WebRTC Chat'),
            'icon'   => 'comments',
            'weight' => 9,
            'children' => array(
                array(
                    'uri'    => 'admin/chat',
                    'title'  => __d('web_chat', 'Public Chat'),
                    'label'  => '',
                    'weight' => 0,
                ),
                array(
                    'uri'    => 'admin/chat/video',
                    'title'  => __d('web_chat', 'New Video Call'),
                    'label'  => '',
                    'weight' => 1,
                ),
            ),
        ),
    );

    return $items;
});
