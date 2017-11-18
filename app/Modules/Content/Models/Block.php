<?php

namespace App\Modules\Content\Models;

use App\Modules\Content\Models\Post;


class Block extends Post
{
    /**
     * @var string
     */
    protected $postType = 'block';
}
