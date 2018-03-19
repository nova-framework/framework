<?php

namespace Modules\Content\Models;

use Modules\Content\Models\Post;


class Block extends Post
{
    /**
     * @var string
     */
    protected $postType = 'block';

    /**
     * @var array
     */
    protected static $aliases = array(
        'block_widget_position'   => array('meta' => 'block_widget_position'),
        'block_handler_class'     => array('meta' => 'block_handler_class'),
        'block_handler_param'     => array('meta' => 'block_handler_param'),
        'block_visibility_path'   => array('meta' => 'block_visibility_path'),
        'block_visibility_mode'   => array('meta' => 'block_visibility_mode'),
        'block_visibility_filter' => array('meta' => 'block_visibility_filter'),
    );
}
