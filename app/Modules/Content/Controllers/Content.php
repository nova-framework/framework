<?php

namespace App\Modules\Content\Controllers;

use Nova\Http\Request;
use Nova\Support\Facades\Config;
use Nova\Support\Str;

use App\Modules\Content\Models\Post;
use App\Modules\Content\Models\Taxonomy;
use App\Modules\Platform\Controllers\BaseController;

use Carbon\Carbon;


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

        $query = Post::with('author', 'taxonomies')->whereIn('status', array('publish', 'password'));

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
        $posts = Post::with('author', 'taxonomies')
            ->where('type', 'post')
            ->whereIn('status', array('publish', 'password'))
            ->orderBy('created_at', 'DESC')
            ->paginate(5);

        return $this->createView(compact('posts'), 'Index')
            ->shares('title', __d('content', 'Frontpage'));
    }

    public function taxonomy($type, $slug)
    {
        $taxonomy = Taxonomy::whereHas('term', function ($query) use ($slug)
        {
            $query->where('slug', $slug);

        })->where('taxonomy', ($type == 'tag') ? 'post_tag' : $type)->firstOrFail();

        $posts = $taxonomy->posts()
            ->with('author', 'taxonomies')
            ->where('type', 'post')
            ->whereIn('status', array('publish', 'password'))
            ->orderBy('created_at', 'DESC')
            ->paginate(5);

        $name = Config::get("content::labels.{$type}.name", Str::title($type));

        return $this->createView(compact('posts'), 'Index')
            ->shares('title', $name .' : ' .$taxonomy->name);
    }

    public function archive($year, $month)
    {
        $posts = Post::with('author', 'taxonomies')
            ->where('type', 'post')
            ->whereIn('status', array('publish', 'password'))
            ->whereYear('created_at', '=', $year)
            ->whereMonth('created_at', '=', $month)
            ->orderBy('created_at', 'DESC')
            ->paginate(5);

        $format = __d('content', '%B %Y');

        $date = Carbon::parse($year .'/' .$month .'/1')->formatLocalized($format);

        return $this->createView(compact('posts'), 'Index')
            ->shares('title', __d('content', 'Archive of {0}', $date));
    }

    public function search(Request $request)
    {
        $search = strip_tags(trim(
            $request->input('query')
        ));

        if (strlen($search) < 5) {
            return Redirect::back()->withStatus(__d('content', 'Invalid query string'), 'danger');
        }

        $posts = Post::with('author', 'taxonomies')
            ->where('type', 'post')
            ->whereIn('status', array('publish', 'password'))
            ->where(function ($query) use ($search)
            {
                $query->where('title', 'LIKE', '%' .$search .'%')->orWhere('content', 'LIKE', '%' .$search .'%');

            })->orderBy('created_at', 'DESC')->paginate(5);

        return $this->createView(compact('posts'), 'Index')
            ->shares('title', __d('content', 'Search results : {0}', htmlentities($search)));
    }
}
