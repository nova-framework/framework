<?php

namespace Modules\Content\Controllers\Admin;

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Http\Request;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Cache;
use Nova\Support\Facades\Event;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Response;

use Modules\Content\Models\Menu;
use Modules\Content\Models\MenuItem;
use Modules\Content\Models\Post;
use Modules\Content\Models\Taxonomy;
use Modules\Content\Models\Term;
use Modules\Platform\Controllers\Admin\BaseController;


class Menus extends BaseController
{

    public function index()
    {
        $menus = Menu::paginate(15);

        return $this->createView()
            ->shares('title', __d('content', 'Menus'))
            ->with('menus', $menus);
    }

    public function store(Request $request)
    {
        $taxonomy = Taxonomy::create(array(
            'taxonomy'    => 'nav_menu',
            'description' => $request->input('description'),
            'parent_id'   => 0,
            'count'       => 0,
        ));

        $term = Term::create(array(
            'id'     => 2,
            'name'   => $name = $request->input('name'),
            'slug'   => Term::uniqueSlug($name, 'nav_menu'),
            'group'  => 0,
        ));

        $taxonomy->term_id = $term->id;

        $taxonomy->save();

        return Redirect::back()->with('success', __d('content', 'The Menu <b>{0}</b> was successfully created.', $name));
    }

    public function update(Request $request, $id)
    {
        try {
            $menu = Menu::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->with('danger', __d('content', 'Menu not found: #{0}', $id));
        }

        $term = $menu->term()->first();

        // Get the original information of the Term.
        $original = $term->name;

        $slug = $term->slug;

        // Update the Term.
        $term->name = $name = $request->input('name');

        $term->slug = Term::uniqueSlug($name, 'nav_menu');

        $term->save();

        // Update the Taxonomy.
        $menu->description = $request->input('description');

        $menu->save();

        // Invalidate the cached menu data.
        Cache::forget('content.menus.' .$slug);

        return Redirect::back()->with('success', __d('content', 'The Menu <b>{0}</b> was successfully updated.', $original));
    }

    public function destroy($id)
    {
        try {
            $menu = Menu::with('items')->findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->with('danger', __d('content', 'Menu not found: #{0}', $id));
        }

        $name = $menu->name;

        $slug = $menu->slug;

        $menu->items->each(function ($item) use ($menu)
        {
            $item->taxonomies()->detach($menu);

            $item->delete();
        });

        $menu->term->delete();

        $menu->delete();

        // Invalidate the cached menu data.
        Cache::forget('content.menus.' .$slug);

        return Redirect::back()->with('success', __d('content', 'The Menu {0} was successfully deleted.', $name));
    }

    public function items($id)
    {
        $authUser = Auth::user();

        try {
            $menu = Menu::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->with('danger', __d('content', 'Menu not found: #{0}', $id));
        }

        $pages = $this->generatePostsListing('page');
        $posts = $this->generatePostsListing('post');

        $categories = $this->generateCategoriesListing();

        return $this->createView()
            ->shares('title', __d('content', 'Manage a Menu'))
            ->with('menu', $menu)
            ->with('pages', $pages)
            ->with('posts', $posts)
            ->with('categories', $categories);
    }

    public function addPost(Request $request, $id)
    {
        $authUser = Auth::user();

        try {
            $taxonomy = Menu::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->with('danger', __d('content', 'Menu not found: #{0}', $id));
        }

        $type = $request->input('type', 'post');

        $posts = $request->input('post', array());

        foreach ($posts as $id) {
            $instance = Post::findOrFail($id);

            $post = Post::create(array(
                'author_id'      => $authUser->id,
                'status'         => 'publish',
                'menu_order'     => $instance->menu_order,
                'type'           => 'nav_menu_item',
                'comment_status' => 'closed',
            ));

            // We need to update this information.
            $post->name = $postId = $post->id;

            $post->guid = site_url('content/' .$postId);

            $post->save();

            // Handle the Metadata.
            $post->saveMeta(array(
                'menu_item_type'             => $type,
                'menu_item_menu_item_parent' => $instance->parent_id,
                'menu_item_object'           => $type,
                'menu_item_object_id'        => $instance->id,
                'menu_item_target'           => null,
                'menu_item_url'              => null,
            ));

            $post->taxonomies()->attach($taxonomy);

            $taxonomy->updateCount();
        }

        // Invalidate the cached menu data.
        Cache::forget('content.menus.' .$taxonomy->slug);

        return Redirect::back()->with('success', __d('content', 'The Menu Item(s) was successfully created.'));
    }

    public function addCategory(Request $request, $id)
    {
        $authUser = Auth::user();

        try {
            $taxonomy = Menu::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->with('danger', __d('content', 'Menu not found: #{0}', $id));
        }

        $categories = $request->input('category', array());

        foreach ($categories as $id) {
            $instance = Taxonomy::findOrFail($id);

            $post = Post::create(array(
                'author_id'      => $authUser->id,
                'status'         => 'publish',
                'menu_order'     => 0,
                'type'           => 'nav_menu_item',
                'comment_status' => 'closed',
            ));

            // We need to update this information.
            $post->name = $postId = $post->id;

            $post->guid = site_url('content/' .$postId);

            $post->save();

            // Handle the Metadata.
            $post->saveMeta(array(
                // Setup the Metadata.
                'menu_item_type'             => 'taxonomy',
                'menu_item_menu_item_parent' => $instance->parent_id,
                'menu_item_object'           => 'category',
                'menu_item_object_id'        => $instance->id,
                'menu_item_target'           => null,
                'menu_item_url'              => null,
            ));

            $post->taxonomies()->attach($taxonomy);

            $taxonomy->updateCount();
        }

        // Invalidate the cached menu data.
        Cache::forget('content.menus.' .$taxonomy->slug);

        return Redirect::back()->with('success', __d('content', 'The Menu Item(s) was successfully created.'));
    }

    public function addCustom(Request $request, $id)
    {
        $authUser = Auth::user();

        try {
            $taxonomy = Menu::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->with('danger', __d('content', 'Menu not found: #{0}', $id));
        }

        $name = $request->input('name');

        $url = $request->input('link');

        if ($request->has('local')) {
            // The link field contains a local URI, not an absolute URL.
            $url = site_url($url);
        }

        $post = Post::create(array(
            'author_id'      => $authUser->id,
            'title'          => $name,
            'status'         => 'publish',
            'menu_order'     => 0,
            'type'           => 'nav_menu_item',
            'comment_status' => 'closed',
        ));

        // We need to update this information.
        $post->name = Post::uniqueName($name);

        $post->guid = site_url('content/' .$post->id);

        $post->save();

        // Handle the Metadata.
        $post->saveMeta(array(
            // Setup the Metadata.
            'menu_item_type'             => 'custom',
            'menu_item_menu_item_parent' => 0,
            'menu_item_object'           => 'custom',
            'menu_item_object_id'        => $post->id,
            'menu_item_target'           => null,
            'menu_item_url'              => $url,
        ));

        $post->taxonomies()->attach($taxonomy);

        $taxonomy->updateCount();

        // Invalidate the cached menu data.
        Cache::forget('content.menus.' .$taxonomy->slug);

        return Redirect::back()->with('success', __d('content', 'The Menu Item(s) was successfully created.'));
    }

    public function updateItem(Request $request, $id, $itemId)
    {
        $authUser = Auth::user();

        try {
            $taxonomy = Menu::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->with('danger', __d('content', 'Menu not found: #{0}', $id));
        }

        try {
            $item = MenuItem::findOrFail($itemId);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->with('danger', __d('content', 'Menu Item not found: #{0}', $itemId));
        }

        $item->title = $request->input('name');

        $item->save();

        // Invalidate the cached menu data.
        Cache::forget('content.menus.' .$taxonomy->slug);

        return Redirect::back()->with('success', __d('content', 'The Menu Item was successfully updated.'));
    }

    public function deleteItem(Request $request, $id, $itemId)
    {
        $authUser = Auth::user();

        try {
            $taxonomy = Menu::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->with('danger', __d('content', 'Menu not found: #{0}', $id));
        }

        try {
            $item = MenuItem::findOrFail($itemId);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->with('danger', __d('content', 'Menu Item not found: #{0}', $itemId));
        }

        $item->taxonomies()->detach($taxonomy);

        $item->delete();

        $taxonomy->updateCount();

        // Invalidate the cached menu data.
        Cache::forget('content.menus.' .$taxonomy->slug);

        return Redirect::back()->with('success', __d('content', 'The Menu Item was successfully deleted.'));
    }

    public function itemsOrder(Request $request, $id)
    {
        try {
            $taxonomy = Menu::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->with('danger', __d('content', 'Menu not found: #{0}', $id));
        }

        $json = $request->get('items');

        $this->updateMenuItemsOrder(
            json_decode($json)
        );

        // Invalidate the cached menu data.
        Cache::forget('content.menus.' .$taxonomy->slug);

        return Redirect::back()->with('success', __d('content', 'The Menu Items order was successfully updated.'));
    }

    /**
     * Update the Items order in a Menu.
     *
     */
    protected function updateMenuItemsOrder(array $items, $parentId = 0)
    {
        foreach ($items as $order => $item) {
            if (is_null($menuItem = MenuItem::find($item->id))) {
                continue;
            }

            $menuItem->parent_id = $parentId;

            $menuItem->menu_order = $order;

            $menuItem->save();

            if (isset($item->children) && ! empty($item->children)) {
                $this->updateMenuItemsOrder($item->children, $menuItem->id);
            }
        }
    }

    protected function generatePostsListing($type, $posts = null, $level = 0)
    {
        $result = '';

        if (is_null($posts)) {
            $posts = Post::where('type', $type)
                ->where('parent_id', 0)
                ->whereIn('status', array('publish', 'password'))
                ->get();
        }

        foreach ($posts as $post) {
            if ($post->type !== $type) {
                continue;
            }

            $result .= '<div class="checkbox" style="padding-left: ' .(($level > 0) ? ($level * 25) .'px' : '') .'"><label><input class="' .$type .'-checkbox" name="post[]" value="' .$post->id .'" type="checkbox">&nbsp;&nbsp;' .$post->title .'</label></div>';

            // Process the children.
            $children = $post->children()
                ->where('type', $type)
                ->whereIn('status', array('publish', 'password'))
                ->get();

            if (! $children->isEmpty()) {
                $level++;

                $result .= $this->generatePostsListing($type, $children, $level);
            }
        }

        return $result;
    }

    protected function generateCategoriesListing($categories = null, $level = 0)
    {
        $result = '';

        if (is_null($categories)) {
            $categories = Taxonomy::where('taxonomy', 'category')
                ->where('parent_id', 0)
                ->get();
        }

        foreach ($categories as $category) {
            if ($category->taxonomy !== 'category') {
                continue;
            }

            $result .= '<div class="checkbox" style="padding-left: ' .(($level > 0) ? ($level * 25) .'px' : '') .'"><label><input class="category-checkbox" name="category[]" value="' .$category->id .'" type="checkbox">&nbsp;&nbsp;' .$category->name .'</label></div>';

            // Process the children.
            $children = $category->children()
                ->where('taxonomy', 'category')
                ->get();

            if (! $children->isEmpty()) {
                $level++;

                $result .= $this->generateCategoriesListing($children, $level);
            }
        }

        return $result;
    }
}
