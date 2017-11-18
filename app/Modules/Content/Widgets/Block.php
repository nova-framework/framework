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
        $post = $this->post;

        // The WYSIHTML5 editor do naughty things with the PHP tags.
        $content = preg_replace('/<!--\?(.*)\?-->/sm', '<?$1?>', $post->getContent());

        return View::make('Widgets/Block', compact('post', 'content'), 'Content')->render();
    }
}
