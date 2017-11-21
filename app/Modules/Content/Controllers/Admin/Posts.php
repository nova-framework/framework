<?php

namespace App\Modules\Content\Controllers\Admin;

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Http\Request;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Cache;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\Event;
use Nova\Support\Facades\Hash;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Response;
use Nova\Support\Facades\URL;
use Nova\Support\Facades\Session;
use Nova\Support\Arr;
use Nova\Support\Str;

use App\Modules\Content\Models\Menu;
use App\Modules\Content\Models\MenuItem;
use App\Modules\Content\Models\Post;
use App\Modules\Content\Models\Tag;
use App\Modules\Content\Models\Term;
use App\Modules\Content\Models\Taxonomy;
use App\Modules\Content\Support\PostType;
use App\Modules\Platform\Controllers\Admin\BaseController;
use App\Modules\Users\Models\User;

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
            ->shares('title', $title);
    }

    public function index(Request $request, $type = null)
    {
        if (! is_null($type)) {
            $type = Str::singular($type);
        } else {
            $type = 'post';
        }

        $postType = PostType::make($type);

        //
        $name = $postType->label('name');

        $statuses = Config::get('content::statuses', array());

        // Get the records.
        $posts = Post::with('author', 'taxonomies')
            ->type($type)
            ->newest()
            ->paginate(15);

        return $this->createView()
            ->shares('title', $postType->label('items'))
            ->with(compact('type', 'name', 'statuses', 'posts', 'postType'));
    }

    public function create(Request $request, $type)
    {
        $authUser = Auth::user();

        $postType = PostType::make($type);

        $name = $postType->label('name');

        //
        $status     = 'draft';
        $visibility = 'public';

        // Create a new Post instance.
        $post = Post::create(array(
            'type'           => $type,
            'status'         => 'draft',
            'author_id'      => $userId = $authUser->id,
            'menu_order'     => 0,
            'comment_status' => ($type == 'post') ? 'open' : 'closed',
        ));

        $post->name = $post->id;

        // Handle the Metadata.
        $post->meta->edit_lock = sprintf('%d:%d', time(), $userId);

        if ($type === 'block') {
            $post->meta->block_handler_class = null;
            $post->meta->block_handler_param = null;
        }

        // Save the Post again, to update its name and metadata.
        $post->save();

        $post->name = '';

        //
        $menuSelect = $this->generateParentSelect();

        $blockTitle = false;
        $blockMode  = 'show';
        $blockPath  = null;

        $categories = $this->generateCategories(
            $ids = $post->taxonomies()->where('taxonomy', 'category')->lists('id')
        );

        $categorySelect = $this->generateCategorySelect();

        $tags = '';

        // Revisions.
        $revisions = $post->newCollection();

        // The last editor.
        $lastEditor = $authUser;

        // Compute the stylesheets needed to be loaded in editor.
        $stylesheets = $this->getDefaultThemeStylesheets();

        //
        $data = compact('post', 'postType', 'status', 'visibility', 'type', 'name', 'categories', 'revisions');

        return $this->createView($data, 'Edit')
            ->shares('title', $postType->label('add_new_item'))
            ->with(compact('categorySelect', 'menuSelect', 'lastEditor', 'tags', 'stylesheets'))
            ->with('creating', true);
    }

    public function edit(Request $request, $id)
    {
        $authUser = Auth::user();

        try {
            $post = Post::with('thumbnail', 'revision')->findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->withStatus(__d('content', 'Record not found: #{0}', $id), 'danger');
        }

        $type = $post->type;

        $postType = PostType::make($type);

        // Handle the Metadata.
        $post->meta->edit_lock = sprintf('%d:%d', time(), $authUser->id);

        // Save the Post, to update its metadata.
        $post->save();

        //
        $name = $postType->label('name');

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

        // Revisions.
        $revisions = $post->revision()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // The last editor.
        $lastEditor = isset($post->meta->edit_last)
            ? User::findOrFail($post->meta->edit_last)
            : $authUser;

        // Compute the stylesheets needed to be loaded in editor.
        $stylesheets = $this->getDefaultThemeStylesheets();

        //
        $data = compact('post', 'postType', 'status', 'visibility', 'type', 'name', 'categories', 'revisions');

        return $this->createView($data, 'Edit')
            ->shares('title', $postType->label('edit_item'))
            ->with(compact('categorySelect', 'menuSelect', 'lastEditor', 'tags', 'stylesheets'))
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
            Session::pushStatus(__d('content', 'Record not found: #{0}', $id), 'danger');

            return Response::json(array('redirectTo' => 'refresh'), 400);
        }

        $postType = PostType::make($post->type);

        $creating = (bool) Arr::get($input, 'creating', 0);

        // Fire the starting event.
        Event::fire('content.post.updating', array($post, $creating));

        //
        $type = $post->type;

        $slug = Arr::get($input, 'slug') ?: Post::uniqueName($input['title'], $post->id);

        // Update the Post instance.
        $post->title   = $input['title'];
        $post->content = $input['content'];
        $post->name    = $slug;

        $post->guid = site_url('content/' .$slug);

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
        else if (($visibility == 'password') && ($status == 'publish')) {
            $status = 'password';

            $password = Hash::make($input['password']);
        }

        $post->status   = $status;
        $post->password = $password;

        if ($type == 'page') {
            $post->parent_id  = (int) Arr::get($input, 'parent', 0);
            $post->menu_order = (int) Arr::get($input, 'order',  0);
        }

        // For the Blocks.
        else if ($type == 'block') {
            $post->menu_order = (int) Arr::get($input, 'order',  0);
        }

        // Handle the MetaData.
        $post->meta->thumbnail_id = (int) $request->input('thumbnail');

        $post->meta->edit_last = $authUser->id;

        if ($type == 'block') {
            $post->meta->block_show_title = (int) $request->input('block-show-title');

            $post->meta->block_visibility_mode = $request->input('block-show-mode');
            $post->meta->block_visibility_path = $request->input('block-show-path');

            $post->meta->block_visibility_filter = $request->input('block-show-filter', 'any');

            $post->meta->block_widget_position = $request->input('block-position');
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

        // Create a new revision from the current Post instance.
        $count = 1;

        $names = $post->revision()->lists('name');

        foreach ($names as $name) {
            if (preg_match('#^(?:\d+)-revision-v(\d+)$#', $name, $matches) === 1) {
                $count = max($count, 1 + (int) $matches[1]);
            }
        }

        $slug = $post->id .'-revision-v' .$count;

        $revision = Post::create(array(
            'content'        => $post->content,
            'title'          => $post->title,
            'excerpt'        => $post->excerpt,
            'status'         => 'inherit',
            'password'       => $post->password,
            'name'           => $slug,
            'parent_id'      => $post->id,
            'guid'           => site_url('content/' .$slug),
            'menu_order'     => $post->menu_order,
            'type'           => 'revision',
            'mime_type'      => $post->mime_type,
            'author_id'      => $authUser->id,
            'comment_status' => 'closed',
        ));

        // Fire the finishing event.
        Event::fire('content.post.updated', array($post, $creating));

        // Invalidate the content caches.
        $this->clearContentCache();

        //
        $status = __d('content', 'The {0} <b>#{1}</b> was successfully saved.', $postType->label('name'), $post->id);

        Session::pushStatus($status, 'success');

        return Response::json(array(
            'redirectTo' => site_url('admin/content/' .Str::plural($type))

        ), 200);
    }

    public function destroy($id)
    {
        try {
            $post = Post::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->withStatus(__d('content', 'Record not found: #{0}', $id), 'danger');
        }

        $postType = PostType::make($post->type);

        // Fire the starting event.
        Event::fire('content.post.deleting', array($post));

        // Delete the Post.
        $taxonomies = $post->taxonomies;

        $post->taxonomies()->detach();

        $taxonomies->each(function ($taxonomy)
        {
            $taxonomy->updateCount();
        });

        $post->delete();

        // Fire the finishing event.
        Event::fire('content.post.deleted', array($post));

        // Invalidate the content caches.
        $this->clearContentCache();

        return Redirect::back()
            ->withStatus(__d('content', 'The {0} <b>#{1}</b> was successfully deleted.', $postType->label('name'), $post->id), 'success');
    }

    public function restore($id)
    {
        try {
            $revision = Post::with('parent')->where('type', 'revision')->findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->withStatus(__d('content', 'Record not found: #{0}', $id), 'danger');
        }

        $post = $revision->parent;

        $postType = PostType::make($post->type);

        // Restore the Post's title, content and excerpt.
        $post->content = $revision->content;
        $post->excerpt = $revision->excerpt;
        $post->title   = $revision->title;

        // Handle the MetaData.
        if (! preg_match('#^(?:\d+)-revision-v(\d+)$#', $revision->name, $matches)) {
            $version = 0;
        } else {
            $post->meta->version = $version = (int) $matches[1];
        }

        $post->save();

        // Invalidate the content caches.
        $this->clearContentCache();

        //
        $status = __d('content', 'The {0} <b>#{1}</b> was successfully restored to the revision: <b>{2}</b>', $postType->label('name'), $post->id, $version);

        return Redirect::back()->withStatus($status, 'success');
    }

    public function revisions($id)
    {
        try {
            $post = Post::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->withStatus(__d('content', 'Record not found: #{0}', $id), 'danger');
        }

        $postType = PostType::make($post->type);

        $revisions = $post->revision()
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        //
        $name = $postType->label('name');

        return $this->createView(compact('type', 'name', 'post', 'revisions'))
            ->shares('title', __d('content', 'Revisions of the {0} : {1}', $name, $post->title));
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
            $taxonomy->load('term');

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

    protected function getDefaultThemeStylesheets()
    {
        $stylesheets = array();

        $theme = Config::get('app.theme');

        $results = Event::fire('content.editor.stylesheets.' .Str::snake($theme), array($theme));

        foreach ($results as $result) {
            if (is_array($result) && ! empty($result)) {
                $stylesheets = array_merge($stylesheets, $result);
            }
        }

        return $stylesheets;
    }
}
