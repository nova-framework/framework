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
            'uri'    => 'admin/chat/video',
            'title'  => __d('video_chat', 'Video Chat'),
            'label'  => '',
            'icon'   => 'comments',
            'weight' => 9,
        ),
    );

    return $items;
});
