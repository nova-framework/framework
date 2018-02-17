<?php

namespace Modules\Content\Models;

use Modules\Content\Models\Post;


class Block extends Post
{
    /**
     * @var string
     */
    protected $postType = 'block';
}
