<?php

namespace App\Modules\Content\Widgets;

use Nova\Support\Facades\Cache;
use Nova\Support\Facades\View;

use Shared\Widgets\Widget;

use App\Modules\Content\Models\Post;

use Carbon\Carbon;


class Block extends Widget
{
    /**
     * @var \App\Modules\Content\Models\Post
     */
    protected $post;


    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function render()
    {
        return View::make('Widgets/Block', array('post' => $this->post), 'Content')->render();
    }
}
