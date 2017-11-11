<?php

namespace App\Modules\Content\Controllers;

use App\Modules\Platform\Controllers\BaseController;

use App\Modules\Content\Models\Post;


class Posts extends BaseController
{
    /**
     * The currently used Theme.
     *
     * @var mixed
     */
    protected $theme = 'Bootstrap';

    /**
     * The currently used Layout.
     *
     * @var mixed
     */
    protected $layout = 'Content';


    public function index($slug)
    {
        $post = Post::status('publish')->slug($slug)->firstOrFail();

        return $this->createView()
            ->shares('title', $post->title)
            ->with('post', $post);
    }
}
