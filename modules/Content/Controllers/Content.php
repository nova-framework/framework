<?php

namespace Modules\Content\Controllers;

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Http\Request;
use Nova\Support\Facades\App;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Cache;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\Hash;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Response;
use Nova\Support\Facades\Session;
use Nova\Support\Facades\View;
use Nova\Support\Str;

use Shared\Support\ReCaptcha;

use App\Controllers\BaseController;

use Modules\Content\Support\Facades\PostType;

use Modules\Content\Models\Post;
use Modules\Content\Models\Taxonomy;

use Carbon\Carbon;

use Exception;


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

        $post = Cache::remember('content.posts.' .$name, 1440, function () use ($name)
        {
            $query = Post::with('author', 'thumbnail', 'taxonomies', 'comments')
                ->where('type', '!=', 'block')
                ->whereIn('status', array('publish', 'password', 'inherit'));

            if (is_numeric($name)) {
                $query->where('id', (int) $name);
            } else {
                $query->where('name', $name);
            }

            if (is_null($post = $query->first())) {
                return null;
            }

            // If the Post is a Revision.
            else if ((($post->type === 'revision') || ($post->type === 'attachment')) && ($post->status === 'inherit')) {
                $parent = $post->parent()->first();

                if (! in_array($parent->status, array('publish', 'password'))) {
                    return null;
                }
            }

            return $post;
        });

        if (is_null($post)) {
            App::abort(404);
        }

        $postType = PostType::make($post->type);

        if (! $postType->public() && Auth::guest()) {
            App::abort(403);
        }

        return View::make($postType->view(), compact('post'))
            ->shares('title', $post->title);
    }

    public function homepage()
    {
        if (is_null($name = Config::get('content::frontpage'))) {
            return $this->frontpage();
        }

        $post = Cache::remember('content.homepage', 1440, function () use ($name)
        {
            return Post::with('author', 'thumbnail')
                ->where('type', 'page')
                ->whereIn('status', array('publish', 'password'))
                ->where('name', $name)
                ->first();
        });

        if (is_null($post)) {
            App::abort(500);
        }

        return $this->createView(compact('post'), 'Page')
            ->shares('title', $post->title);
    }

    public function frontpage()
    {
        $posts = Post::with('author', 'thumbnail', 'taxonomies')
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
            ->with('author', 'thumbnail', 'taxonomies')
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
        $posts = Post::with('author', 'thumbnail', 'taxonomies')
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
            return Redirect::back()->with('danger', __d('content', 'Invalid query string'));
        }

        $posts = Post::with('author', 'thumbnail', 'taxonomies')
            ->where('type', 'post')
            ->whereIn('status', array('publish', 'password'))
            ->where(function ($query) use ($search)
            {
                $query->where('title', 'LIKE', '%' .$search .'%')->orWhere('content', 'LIKE', '%' .$search .'%');

            })->orderBy('created_at', 'DESC')->paginate(5);

        return $this->createView(compact('posts'), 'Index')
            ->shares('title', __d('content', 'Search results : {0}', htmlentities($search)));
    }

    public function unlock(Request $request, $id)
    {
        // Verify the submitted reCAPTCHA
        if (! Auth::check() && ! ReCaptcha::check($request->input('g-recaptcha-response'), $request->ip())) {
            return Redirect::back()->with('danger', __d('content', 'The reCaptcha verification failed.'));
        }

        try {
            $post = Post::where('status', 'password')->findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->with('danger', __d('content', 'Record not found: #{0}', $id));
        }

        if (! Hash::check($request->input('password'), $post->password)) {
            return Redirect::back()->with('danger', __d('content', 'The password is not valid.'));
        }

        Session::set('content-unlocked-post-' .$post->id, true);

        return Redirect::back();
    }
}
