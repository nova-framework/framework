<?php

namespace App\Modules\Content\Controllers\Admin;

use Nova\Http\Request;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Config;
use Nova\Support\Str;

use App\Modules\Content\Models\Post;
use App\Modules\Content\Models\Taxonomy;
use App\Modules\Platform\Controllers\Admin\BaseController;

use Faker\Factory as FakerFactory;


class Posts extends BaseController
{

    public function taxonomy(Request $request, $type, $slug)
    {
        $statuses = Config::get('content::statuses', array());

        $name = Config::get('content::labels.' .$type .'.name', Str::title($type));

        // Get the taxonomy.
        $type = ($type == 'tag') ? 'post_tag' : 'category';

        $taxonomy = Taxonomy::where('taxonomy', $type)->slug($slug)->first();

        //
        $title = __d('content', 'Posts in the {0} : {1}', $name, $taxonomy->name);
        $name  = __d('content', 'Post');

        // Get the records.
        $posts = $taxonomy->posts()
            ->where('type', 'post')
            ->newest()
            ->paginate(15);

        return $this->createView(compact('type', 'name', 'statuses', 'posts'), 'Index')
            ->shares('title', $title)
            ->with('simple', true);
    }

    public function index(Request $request, $type = null)
    {
        if (! is_null($type)) {
            $type = Str::singular($type);
        } else {
            $type = 'post';
        }

        $name  = Config::get('content::labels.' .$type .'.name', Str::title($type));
        $title = Config::get('content::labels.' .$type .'.title', Str::title(Str::plural($type)));

        $statuses = Config::get('content::statuses', array());

        // Get the records.
        $posts = Post::with('author', 'taxonomies')
            ->type($type)
            ->newest()
            ->paginate(15);

        return $this->createView()
            ->shares('title', $title)
            ->with(compact('type', 'name', 'statuses', 'posts'));
    }

    public function create(Request $request, $type)
    {
        $name  = Config::get('content::labels.' .$type .'.name', Str::title($type));
        $mode = Config::get('content::labels.' .$type .'.title', Str::title(Str::plural($type)));

        $mainCategory = Taxonomy::category()->whereHas('term', function ($query)
        {
            $query->where('slug', 'uncategorized');

        })->first();

        $status = 'draft';

        // Create a new Post instance.
        $post = new Post(array(
            'type'           => $type,
            'status'         => 'draft',
            'comment_status' => ($type == 'page') ? 'closed' : 'open',
        ));

        $categories = $this->generateCategoriesSelect();

        return $this->createView(compact('post', 'status', 'type', 'name', 'mode', 'categories'), 'Edit')
            ->shares('title', __d('content', 'Create a new {0}', $name));
    }

    public function sample(Request $request)
    {
        $authUser = Auth::user();

        //
        $faker = FakerFactory::create();

        $title = rtrim($faker->sentence(5), '.');

        $text = '';

        for ($i = 0; $i < 15; $i++) {
            $text .= '<p style="text-align: justify;">' .$faker->realText($faker->numberBetween(100, 1000)) .'</p>';
        }

        $name = Post::uniqueName($title);

        //
        $post = Post::create(array(
            'content'    => $text,
            'title'      => $title,
            'status'     => 'publish',
            'type'       => 'post',
            'name'       => $name,
            'author_id'  => $authUser->id,
        ));

        //
        $post->taxonomies()->sync(array(1, 2));

        $post->taxonomies->each(function ($taxonomy)
        {
            $taxonomy->updateCount();
        });

        $posts = Post::all();

        dd($posts);
    }

    protected function generateCategoriesSelect(array $categories = array(), $taxonomies = null, $level = 0)
    {
        $result = '';

        if (is_null($taxonomies)) {
            $taxonomies = Taxonomy::with('children')->where('taxonomy', 'category')->where('parent_id', 0)->get();
        }

        foreach ($taxonomies as $taxonomy) {
            $result .= '<option value="' .$taxonomy->id .'"' .(in_array($taxonomy->id, $categories) ? ' selected="selected"' : '') .'>' .trim(str_repeat('--', $level) .' ' .$taxonomy->name) .'</option>' ."\n";

            // Process the children.
            $taxonomy->load('children');

            if (! $taxonomy->children->isEmpty()) {
                $level++;

                $result .= $this->generateCategoriesSelect($categories, $taxonomy->children, $level);
            }
        }

        return $result;
    }
}