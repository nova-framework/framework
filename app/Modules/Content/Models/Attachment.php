<?php

namespace App\Modules\Content\Models;

use App\Modules\Content\Models\Post;


class Attachment extends Post
{
    /**
     * @var string
     */
    protected $postType = 'attachment';

    /**
     * @var array
     */
    protected $appends = array(
        'url',
        'type',
        'description',
        'caption',
        'alt',
    );

    /**
     * @var array
     */
    protected static $aliases = array(
        'url'         => 'guid',
        'type'        => 'mime_type',
        'description' => 'content',
        'caption'     => 'excerpt',
        'alt'         => array(
            'meta' => 'attachment_image_alt'
        ),
    );
}
