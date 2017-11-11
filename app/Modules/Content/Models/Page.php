<?php

namespace App\Modules\Content\Models;

use App\Modules\Content\Models\Post;


class Page extends Post
{
    /**
     * @var string
     */
    protected $postType = 'page';
}
