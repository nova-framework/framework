<?php

namespace Modules\Content\Models;

use Modules\Content\Models\Post;


class Page extends Post
{
    /**
     * @var string
     */
    protected $postType = 'page';
}
