<?php

namespace App\Modules\Content\Controllers;

use Nova\Support\Str;

use App\Modules\Platform\Controllers\BaseController;

use App\Modules\Content\Models\Post;


class Content extends BaseController
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


    public function index($name = null)
    {
        if (is_null($name)) {
            return $this->frontpage();
        }

        $query = Post::whereIn('status', array('publish', 'password'));

        if (is_numeric($name)) {
            $query->where('id', (int) $name);
        } else {
            $query->where('name', $name);
        }

        $post = $query->firstOrFail();

        // Calculate the View used for rendering this Post instance.
        $view = ($post->type == 'page') ? 'Page' : 'Post';

        return $this->createView(compact('post'), $view)
            ->shares('title', $post->title);
    }

    public function frontpage()
    {
        $posts = Post::where('type', 'post')
            ->whereIn('status', array('publish', 'password'))
            ->orderBy('created_at', 'DESC')
            ->paginate(5);

        return $this->createView(compact('posts'), 'Index')
            ->shares('title', __d('content', 'Frontpage'));
    }
}
