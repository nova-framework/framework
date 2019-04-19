<?php

namespace Modules\Content\Controllers\Admin;

use Nova\Auth\UserInteface as User;
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


class MenuItems extends BaseController
{

    public function index($id)
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

        $categories = $this->generateTaxonomiesListing('category');

        return $this->createView()
            ->shares('title', __d('content', 'Manage a Menu'))
            ->with('menu', $menu)
            ->with('pages', $pages)
            ->with('posts', $posts)
            ->with('categories', $categories);
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
            $result .= '<div class="checkbox" style="padding-left: ' .(($level > 0) ? ($level * 25) .'px' : '') .'"><label><input class="' .$type .'-checkbox" name="' .$type .'[]" value="' .$post->id .'" type="checkbox">&nbsp;&nbsp;' .$post->title .'</label></div>';

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

    protected function generateTaxonomiesListing($type, $taxonomies = null, $level = 0)
    {
        $result = '';

        if (is_null($taxonomies)) {
            $taxonomies = Taxonomy::where('taxonomy', $type)->where('parent_id', 0)->get();
        }

        foreach ($taxonomies as $taxonomy) {
            $result .= '<div class="checkbox" style="padding-left: ' .(($level > 0) ? ($level * 25) .'px' : '') .'"><label><input class="' .$type .'-checkbox" name="' .$type .'[]" value="' .$taxonomy->id .'" type="checkbox">&nbsp;&nbsp;' .$taxonomy->name .'</label></div>';

            // Process the children.
            $children = $taxonomy->children()->where('taxonomy', $type)->get();

            if (! $children->isEmpty()) {
                $level++;

                $result .= $this->generateTaxonomiesListing($type, $children, $level);
            }
        }

        return $result;
    }

    public function store(Request $request, $id, $mode)
    {
        try {
            $menu = Menu::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->with('danger', __d('content', 'Menu not found: #{0}', $id));
        }

        $this->createMenuItems($request, $menu, $mode);

        $menu->updateCount();

        // Invalidate the cached menu data.
        Cache::forget('content.menus.' .$menu->slug);

        return Redirect::back()->with('success', __d('content', 'The Menu Item(s) was successfully created.'));
    }

    protected function createMenuItems(Request $request, Menu $menu, $mode)
    {
        $authUser = Auth::user();

        if ($mode == 'custom') {
            // Here we should validate the custom links.

            return $this->createCustomLink($request, $taxonomy, $authUser);
        }

        // Here we should validate the posts and taxonomies.

        if ($mode == 'posts') {
            return $this->createPostLinks($request, $taxonomy, $authUser);
        }

        return $this->createTaxonomyLinks($request, $taxonomy, $authUser);
    }

    protected function createCustomLink(Request $request, Menu $menu, User $authUser)
    {
        $type = 'custom';

        //
        $url = $request->input('link');

        if ($request->has('local')) {
            // The link field contains a local URI, not an absolute URL.
            $url = site_url($url);
        }

        $name = $request->input('name');

        // Create a Menu Link instance.
        $menuLink = Post::create(array(
            'author_id'      => $authUser->id,
            'title'          => $name,
            'status'         => 'publish',
            'menu_order'     => 0,
            'type'           => 'nav_menu_item',
            'comment_status' => 'closed',
        ));

        // We need to update this information.
        $menuLink->guid = site_url('content/{0}', $menuLink->id);

        $menuLink->name = Post::uniqueName($name);

        $menuLink->save();

        // Handle the Metadata.
        $menuLink->saveMeta(array(
            'menu_item_type'             => 'custom',
            'menu_item_menu_item_parent' => 0,
            'menu_item_object'           => $type,
            'menu_item_object_id'        => $post->id,
            'menu_item_target'           => null,
            'menu_item_url'              => $url,
        ));

        $menuLink->taxonomies()->attach($menu);
    }

    protected function createPostLinks(Request $request, Menu $menu, User $authUser)
    {
        $ids = static::filterModelIds(
            $request->input($type, array())
        );

        $type = $request->input('type', 'post');

        //
        $posts = Post::where('type', $type)->whereIn('id', $ids);

        $posts->each(function ($post) use ($type, $menu, $authUser)
        {
            $menuLink = Post::create(array(
                'author_id'      => $authUser->id,
                'status'         => 'publish',
                'menu_order'     => $post->menu_order,
                'type'           => 'nav_menu_item',
                'comment_status' => 'closed',
            ));

            // We need to update this information.
            $menuLink->guid = site_url('content/{0}', $name = $menuLink->id);

            $menuLink->name = $name;

            $menuLink->save();

            // Handle the Metadata.
            $menuLink->saveMeta(array(
                'menu_item_type'             => 'post',
                'menu_item_menu_item_parent' => $post->parent_id,
                'menu_item_object'           => $type,
                'menu_item_object_id'        => $post->id,
                'menu_item_target'           => null,
                'menu_item_url'              => null,
            ));

            $menuLink->taxonomies()->attach($menu);
        });
    }

    protected function createTaxonomyLinks(Request $request, Menu $menu, User $authUser)
    {
        $ids = static::filterModelIds(
            $request->input($type, array())
        );

        $type = $request->input('type', 'category');

        //
        $taxonomies = Taxonomy::where('taxonomy', $type)->whereIn('id', $ids);

        $taxonomies->each(function ($taxonomy) use ($type, $menu, $authUser)
        {
            $menuLink = Post::create(array(
                'author_id'      => $authUser->id,
                'status'         => 'publish',
                'menu_order'     => 0,
                'type'           => 'nav_menu_item',
                'comment_status' => 'closed',
            ));

            // We need to update this information.
            $menuLink->guid = site_url('content/{0}', $name = $menuLink->id);

            $menuLink->name = $name;

            $menuLink->save();

            // Handle the Metadata.
            $menuLink->saveMeta(array(
                // Setup the Metadata.
                'menu_item_type'             => 'taxonomy',
                'menu_item_menu_item_parent' => $taxonomy->parent_id,
                'menu_item_object'           => $type,
                'menu_item_object_id'        => $taxonomy->id,
                'menu_item_target'           => null,
                'menu_item_url'              => null,
            ));

            $menuLink->taxonomies()->attach($menu);
        });
    }

    public function update(Request $request, $menuId, $itemId)
    {
        $authUser = Auth::user();

        try {
            $taxonomy = Menu::findOrFail($menuId);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->with('danger', __d('content', 'Menu not found: #{0}', $menuId));
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

    public function destroy(Request $request, $menuId, $itemId)
    {
        $authUser = Auth::user();

        try {
            $taxonomy = Menu::findOrFail($menuId);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->with('danger', __d('content', 'Menu not found: #{0}', $menuId));
        }

        try {
            $item = MenuItem::findOrFail($itemId);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->with('danger', __d('content', 'Menu Item not found: #{0}', $itemId));
        }

        $item->taxonomies()->detach($taxonomy);

        $item->delete();

        //
        $taxonomy->updateCount();

        // Invalidate the cached menu data.
        Cache::forget('content.menus.' .$taxonomy->slug);

        return Redirect::back()->with('success', __d('content', 'The Menu Item was successfully deleted.'));
    }

    public function order(Request $request, $id)
    {
        try {
            $taxonomy = Menu::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->with('danger', __d('content', 'Menu not found: #{0}', $id));
        }

        $items = json_decode(
            $request->get('items')
        );

        $this->updateOrder($items, 0);

        // Invalidate the cached menu data.
        Cache::forget('content.menus.' .$taxonomy->slug);

        return Redirect::back()->with('success', __d('content', 'The Menu Items order was successfully updated.'));
    }

    /**
     * Update the Items order in a Menu.
     *
     */
    protected function updateOrder(array $items, $parentId = 0)
    {
        foreach ($items as $order => $item) {
            $menuItem = MenuItem::find($item->id);

            if (! is_null($menuItem)) {
                $menuItem->parent_id = $parentId;

                $menuItem->menu_order = $order;

                $menuItem->save();

                if (isset($item->children) && ! empty($item->children)) {
                    $this->updateOrder($item->children, $menuItem->id);
                }
            }
        }
    }

    protected static function filterModelIds($ids)
    {
        $items = array_map(function ($value)
        {
            return intval($value);

        }, $ids);

        return array_filter(array_unique($items, SORT_NUMERIC), function ($value)
        {
            return ($value > 0);
        });
    }
}
