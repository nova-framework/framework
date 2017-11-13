<?php

namespace App\Modules\Content\Controllers\Admin;

use Nova\Http\Request;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\Hash;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Response;
use Nova\Support\Arr;
use Nova\Support\Str;

use App\Modules\Content\Models\Menu;
use App\Modules\Content\Models\MenuItem;
use App\Modules\Content\Models\Post;
use App\Modules\Content\Models\Tag;
use App\Modules\Content\Models\Term;
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
        $authUser = Auth::user();

        //
        $name  = Config::get('content::labels.' .$type .'.name', Str::title($type));
        $mode = Config::get('content::labels.' .$type .'.title', Str::title(Str::plural($type)));

        $mainCategory = Taxonomy::category()->whereHas('term', function ($query)
        {
            $query->where('slug', 'uncategorized');

        })->first();

        $status     = 'draft';
        $visibility = 'public';

        // Create a new Post instance.
        $post = Post::create(array(
            'type'           => $type,
            'status'         => 'draft',
            'author_id'      => $authUser->id,
            'comment_status' => ($type == 'page') ? 'closed' : 'open',
        ));

        $post->name = $post->id;

        $post->save();

        $post->name = '';

        //
        $menuSelect = $this->generateMenuSelect();

        $categories = $this->generateCategories(
            $ids = $post->taxonomies()->where('taxonomy', 'category')->lists('id')
        );

        $categorySelect = $this->generateCategorySelect();

        $tags = '';

        return $this->createView(compact('post', 'status', 'visibility', 'type', 'name', 'mode', 'categories', 'categorySelect', 'menuSelect'), 'Edit')
            ->shares('title', __d('content', 'Create a new {0}', $name))
            ->with('tags', $tags)
            ->with('creating', true);
    }

    public function edit(Request $request, $id)
    {
        $authUser = Auth::user();

        try {
            $post = Post::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->withStatus(__d('content', 'Record not found: #{0}', $id), 'danger');
        }

        $type = $post->type;

        //
        $name  = Config::get('content::labels.' .$type .'.name', Str::title($type));
        $mode = Config::get('content::labels.' .$type .'.title', Str::title(Str::plural($type)));

        $mainCategory = Taxonomy::category()->whereHas('term', function ($query)
        {
            $query->where('slug', 'uncategorized');

        })->first();

        $status = $post->status;

        if (Str::contains($status, '-')) {
            // The status could be: private-draft and private-review
            list ($visibility, $status) = explode('-', $status, 2);
        } else if ($status == 'password') {
            $status     = 'published';
            $visibility = 'password';
        } else if ($status == 'private') {
            $status     = 'published';
            $visibility = 'private';
        } else {
            $visibility = 'public';
        }

        //
        $categories = $this->generateCategories(
            $ids = $post->taxonomies()->where('taxonomy', 'category')->lists('id')
        );

        $categorySelect = $this->generateCategorySelect($ids);

        $tags = implode(', ',
            $post->taxonomies()->where('taxonomy', 'post_tag')->lists('id')
        );

        // No menu selection on edit mode.
        $menuSelect = '';

        $title = $post->title ?: __d('content', 'Untitled');

        return $this->createView(compact('post', 'status', 'visibility', 'type', 'name', 'mode', 'categories', 'categorySelect', 'menuSelect'), 'Edit')
            ->shares('title', __d('content', 'Edit the {0} : {1}', $name, $title))
            ->with('tags', $tags)
            ->with('creating', false);
    }

    public function update(Request $request, $id)
    {
        $authUser = Auth::user();

        //
        $input = $request->all();

        try {
            $post = Post::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->withStatus(__d('content', 'Record not found: #{0}', $id), 'danger');
        }

        $type = $post->type;

        $slug = Arr::get($input, 'slug') ?: Post::uniqueName($input['title']);

        $creating = (bool) Arr::get($input, 'creating', 0);

        // Update the Post instance.s
        $post->title   = $input['title'];
        $post->content = $input['content'];
        $post->name    = $slug;

        // The Status.
        $status = Arr::get($input, 'status', 'draft');

        if ($creating && ($status === 'draft')) {
            $status = 'publish';
        }

        $visibility = Arr::get($input, 'visibility', 'public');

        $password = null;

        if ($visibility == 'private') {
            // The status could be: private, private-draft and private-review
            if ($status == 'publish') {
                $status = 'private';
            } else {
                $status = 'private-' .$status;
            }
        }

        // Only the published posts can have a password.
        else if (($visibility == 'password') && ($status == 'public')) {
            $status = 'password';

            $password = Hash::make($input['password']);
        }

        $post->status   = $status;
        $post->password = $password;

        // Save the Post instance before to process the Menu Item or Categories and Tags.
        $post->save();

        if ($type == 'page') {
            $parentId = Arr::get($input, 'parent', 0);

            $order = Arr::get($input, 'order', 0);

            if ($creating) {
                $item = MenuItem::create(array(
                    'author_id'      => $authUser->id,
                    'status'         => 'publish',
                    'menu_order'     => $order,
                    'type'           => 'nav_menu_item',
                    'comment_status' => 'closed',
                ));

                $item->name = $item->id;

                $item->guid = url('content', $item->id);

                // Setup the Metadata.
                $item->meta->menu_item_type             = 'page';
                $item->meta->menu_item_menu_item_parent = $parentId;
                $item->meta->menu_item_object           = 'page';
                $item->meta->menu_item_object_id        = $post->id;
                $item->meta->menu_item_target           = null;
                $item->meta->menu_item_url              = null;

                $item->save();

                // Update the Menu information.
                $menu = Menu::firstOrFail();

                $menu->items()->attach($item);

                $menu->updateCount();
            }

            // We update an existent Post.
            else {
                $item = MenuItem::whereHas('meta', function ($query) use ($post)
                {
                    $query->where('meta->menu_item_object', $post->type)->where('menu_item_object_id', $post->id);

                })->first();

                if (! is_null($item)) {
                    $item->menu_order = $order;

                    $item->save();
                }
            }
        }

        // This is a Post type.
        else if ($type == 'post') {
            // Update the Post categories.
            $result = Arr::get($input, 'categories');

            $categories = array();

            if (! empty($result)) {
                $items = explode('&', $result);

                foreach ($items as $item) {
                    list (, $value) = explode('=', $item);

                    $categories[] = (int) $value;
                }
            }

            $post->taxonomies()->sync($categories);

            // Update the Post tags.
            $tags = Arr::has($input, 'tags') ? explode(',', Arr::get($input, 'tags')) : array();

            $ids = array();

            foreach ($tags as $name) {
                $name = trim($name);

                $tag = Taxonomy::where('taxonomy', 'post_tag')->whereHas('term', function ($query) use ($name)
                {
                    $query->where('name', $name);

                })->first();

                if (is_null($tag)) {
                    $term = Term::create(array(
                        'name'   => $name,
                        'slug'   => Term::uniqueSlug($name, 'post_tag'),
                    ));

                    $tag = Taxonomy::create(array(
                        'term_id'     => $term->id,
                        'taxonomy'    => 'tag',
                        'description' => '',
                    ));
                }

                $ids[] = $tag->id;
            }

            if (! empty($ids)) {
                $post->taxonomies()->attach($ids);
            }

            // Update the count in the associated taxonomies.
            $post->taxonomies->each(function ($taxonomy)
            {
                $taxonomy->updateCount();
            });
        }

        //
        $name = Config::get("content::labels.{$type}.name", Str::title($type));

        $status = $creating
            ? __d('content', 'The {0} <b>#{1}</b> was successfully created.', $name, $post->id)
            : __d('content', 'The {0} <b>#{1}</b> was successfully updated.', $name, $post->id);

        return Redirect::to('admin/content/' .Str::plural($type))->withStatus($status, 'success');
    }

    public function destroy($id)
    {
        try {
            $post = Post::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->withStatus(__d('content', 'Record not found: #{0}', $id), 'danger');
        }

        $taxonomies = $post->taxonomies;

        $post->taxonomies()->detach();

        $taxonomies->each(function ($taxonomy)
        {
            $taxonomy->updateCount();
        });

        $post->delete();

        return Redirect::back()
            ->withStatus(__d('content', 'The record <b>#{0}</b> was successfully deleted.', $post->id), 'success');
    }

    public function tags(Request $request, $id)
    {
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

    protected function generateCategories(array $categories = array(), $taxonomies = null, $level = 0)
    {
        $result = '';

        if (is_null($taxonomies)) {
            $taxonomies = Taxonomy::with('children')->where('taxonomy', 'category')->where('parent_id', 0)->get();
        }

        foreach ($taxonomies as $taxonomy) {
            $result .= '<div class="checkbox"><label><input class="category-checkbox" name="category[]" value="' .$taxonomy->id .'" type="checkbox" ' .(in_array($taxonomy->id, $categories) ? ' checked="checked"' : '') .'> ' .trim(str_repeat('--', $level) .' ' .$taxonomy->name) .'</label></div>';

            // Process the children.
            $taxonomy->load('children');

            if (! $taxonomy->children->isEmpty()) {
                $level++;

                $result .= $this->generateCategories($categories, $taxonomy->children, $level);
            }
        }

        return $result;
    }

    protected function generateCategorySelect(array $categories = array(), $taxonomies = null, $level = 0)
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

                $result .= $this->generateCategorySelect($categories, $taxonomy->children, $level);
            }
        }

        return $result;
    }

    protected function generateMenuSelect($menu = 'nav_menu', $parentId = 0, $items = null, $level = 0)
    {
        $result = '';

        if (is_null($items)) {
            $menu = Menu::firstOrFail();

            $items = $menu->items->where('parent_id', 0);

            //
            $result = '<option value="0"' .(($parentId == 0) ? ' selected="selected"' : '') .'>' .__d('content', '(no parent)') .'</option>';
        }

        foreach ($items as $item) {
            $instance = $item->instance();

            $result .= '<option value="' .$item->id .'"' .(($item->id == $parentId) ? ' selected="selected"' : '') .'>' .trim(str_repeat('--', $level) .' ' .$instance->title) .'</option>' ."\n";

            // Process the children.
            $item->load('children');

            if (! $item->children->isEmpty()) {
                $level++;

                $result .= $this->generateMenuSelect($menu, $parentId, $item->children, $level);
            }
        }

        return $result;
    }
}
