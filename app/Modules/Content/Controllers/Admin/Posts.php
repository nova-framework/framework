<?php

namespace App\Modules\Content\Controllers\Admin;

use Nova\Http\Request;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Cache;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\Event;
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
            'author_id'      => $userId = $authUser->id,
            'menu_order'     => 0,
            'comment_status' => ($type == 'page') ? 'closed' : 'open',
        ));

        $post->name = $post->id;

        // Metadata.
        $post->meta->edit_last = $userId;

        $post->meta->edit_lock = sprintf('%d:%d', time(), $userId);

        // Save the Post again, to update its name and metadata.
        $post->save();

        $post->name = '';

        //
        $menuSelect = $this->generateParentSelect();

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

        // Metadata.
        $post->meta->edit_last = $userId = $authUser->id;

        $post->meta->edit_lock = sprintf('%d:%d', time(), $userId);

        // Save the Post, to update its metadata.
        $post->save();

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
        }

        // We should compute every field.
        else if ($status == 'password') {
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

        // The Tags.
        $tags = $post->taxonomies()->where('taxonomy', 'post_tag')->get();

        $tags = $tags->map(function ($tag)
        {
            return '<div class="tag-item"><a class="delete-tag-link" href="#" data-name="' .$tag->name  .'" data-id="' .$tag->id  .'"><i class="fa fa-times-circle"></i></a> ' .$tag->name .'</div>';

        })->implode("\n");

        // No menu selection on edit mode.
        $menuSelect = '';

        return $this->createView(compact('post', 'status', 'visibility', 'type', 'name', 'mode', 'categories', 'categorySelect', 'menuSelect'), 'Edit')
            ->shares('title', __d('content', 'Edit a {0}', $name))
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

        $slug = Arr::get($input, 'slug') ?: Post::uniqueName($input['title'], $post->id);

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
            $status = ($status == 'publish') ? 'private' : 'private-' .$status;
        }

        // Only the published posts can have a password.
        else if (($visibility == 'password') && ($status == 'public')) {
            $status = 'password';

            $password = Hash::make($input['password']);
        }

        $post->status   = $status;
        $post->password = $password;

        if ($type == 'page') {
            $post->parent_id = (int) Arr::get($input, 'parent', 0);

            $post->menu_order = (int) Arr::get($input, 'order', 0);
        }

        // Save the Post instance before to process the Categories and Tags.
        $post->save();

        if ($type == 'post') {
            $categories = array();

            if (! empty($result = Arr::get($input, 'categories'))) {
                // The value is something like: 'category[]=1&category[]=3&category[]=4'

                $categories = array_map(function ($item)
                {
                    list (, $value) = explode('=', $item);

                    return (int) $value;

                }, explode('&', urldecode($result)));
            }

            $taxonomies = $post->taxonomies()
                ->where('taxonomy', 'category')
                ->lists('id');

            if (! empty($ids = array_diff($taxonomies, $categories))) {
                $post->taxonomies()->detach($ids);
            }

            if (! empty($ids = array_diff($categories, $taxonomies))) {
                $post->taxonomies()->attach($ids);
            }

            // Update the count field in the affected taxonomies.
            $ids = array_unique(array_merge($taxonomies, $categories));

            $taxonomies = Taxonomy::whereIn('id', $ids)->get();

            $taxonomies->each(function ($taxonomy)
            {
                $taxonomy->updateCount();
            });
        }

        // Fire the associated event.
        Event::fire('content.post.updated', array($post, $creating));

        // Invalidate the content caches.
        $this->clearContentCache();

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

        // Invalidate the content caches.
        $this->clearContentCache();

        return Redirect::back()
            ->withStatus(__d('content', 'The record <b>#{0}</b> was successfully deleted.', $post->id), 'success');
    }

    public function addTags(Request $request, $id)
    {
        try {
            $post = Post::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Response::json(array('error' => 'Not Found'), 400);
        }

        // Get the actual Tag instances associated to this Post.
        $items = $post->taxonomies()->where('taxonomy', 'post_tag')->get();

        // Get the names of the already associated tags.
        $existentTags = $items->map(function ($item)
        {
            return $item->name;

        })->toArray();

        //
        $requestTags = array();

        if (! empty($tags = $request->input('tags'))) {
            // The tags value is something like: 'Sample Tag, Another Tag, Testings'

            $requestTags = array_map('trim', explode(',', $tags));
        } else {
            return Response::json(array('error' => 'The Tags value is required'), 400);
        }

        $taxonomies = array();

        foreach ($requestTags as $name) {
            if (in_array($name, $existentTags)) {
                continue;
            }

            $tag = Taxonomy::where('taxonomy', 'post_tag')->whereHas('term', function ($query) use ($name)
            {
                $query->where('name', $name);

            })->first();

            if (! is_null($tag)) {
                array_push($taxonomies, $tag);

                continue;
            }

            $slug = Term::uniqueSlug($name, 'post_tag');

            $term = Term::create(array(
                'name'   => $name,
                'slug'   => $slug,
            ));

            $tag = Taxonomy::create(array(
                'term_id'     => $term->id,
                'taxonomy'    => 'post_tag',
                'description' => '',
            ));

            array_push($taxonomies, $tag);
        }

        $result = array();

        foreach ($taxonomies as $taxonomy) {
            $post->taxonomies()->attach($taxonomy);

            $taxonomy->updateCount();

            array_push($result, array(
                'id'   => $taxonomy->id,
                'name' => $taxonomy->name
            ));
        }

        return Response::json($result, 200);
    }

    public function detachTag(Request $request, $id, $tagId)
    {
        try {
            $post = Post::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Response::json(array('error' => 'Not Found'), 400);
        }

        $post->taxonomies()->detach($tagId);

        // Update the count field in the associated taxonomies.
        $post->taxonomies->each(function ($taxonomy)
        {
            $taxonomy->updateCount();
        });

        return Response::json(array('success' => true), 200);
    }

    protected function generateCategories(array $categories = array(), $taxonomies = null, $level = 0)
    {
        $result = '';

        if (is_null($taxonomies)) {
            $taxonomies = Taxonomy::with('children')->where('taxonomy', 'category')->where('parent_id', 0)->get();
        }

        foreach ($taxonomies as $taxonomy) {
            $result .= '<div class="checkbox" style="padding-left: ' .(($level > 0) ? ($level * 25) .'px' : '') .'"><label><input class="category-checkbox" name="category[]" value="' .$taxonomy->id .'" type="checkbox" ' .(in_array($taxonomy->id, $categories) ? ' checked="checked"' : '') .'> ' .$taxonomy->name .'</label></div>';

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

    protected function generateParentSelect($menu = 'nav_menu', $parentId = 0, $items = null, $level = 0)
    {
        $result = '';

        if (is_null($items)) {
            $items = Post::where('type', 'page')
                ->whereIn('status', array('publish', 'password'))
                ->where('parent_id', 0)
                ->get();

            //
            $result = '<option value="0"' .(($parentId == 0) ? ' selected="selected"' : '') .'>' .__d('content', '(no parent)') .'</option>';
        }

        foreach ($items as $item) {
            $result .= '<option value="' .$item->id .'"' .(($item->id == $parentId) ? ' selected="selected"' : '') .'>' .trim(str_repeat('--', $level) .' ' .$item->title) .'</option>' ."\n";

            // Process the children.
            $children = $item->children()
                ->where('type', 'page')
                ->whereIn('status', array('publish', 'password'))
                ->get();

            if (! $children->isEmpty()) {
                $level++;

                $result .= $this->generateParentSelect($menu, $parentId, $children, $level);
            }
        }

        return $result;
    }

    protected function clearContentCache()
    {
        Cache::forget('content.categories');
        Cache::forget('content.archives');
    }
}
