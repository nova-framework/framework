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
use Nova\Support\Facades\Validator;
use Nova\Support\Facades\View;
use Nova\Support\Arr;
use Nova\Support\Collection;

use Modules\Content\Models\Menu;
use Modules\Content\Models\MenuItem;
use Modules\Content\Models\Post;
use Modules\Content\Models\Taxonomy;
use Modules\Content\Models\Term;
use Modules\Content\Platform\ContentType;
use Modules\Content\Support\Facades\PostType;
use Modules\Content\Support\Facades\TaxonomyType;
use Modules\Platform\Controllers\Admin\BaseController;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;


class MenuItems extends BaseController
{

    protected function validator(array $data, $local)
    {
        $rules = array(
            'link' => 'required|' . ($local ? 'valid_uri' : 'url'),
            'name' => 'required|valid_name',
        );

        $messages = array(
            'valid_uri'  => __d('content', 'The :attribute field is not a valid URI.'),
            'valid_name' => __d('content', 'The :attribute field is not a valid name.'),
        );

        // Add the custom Validation Rule commands.
        Validator::extend('valid_uri', function ($attribute, $value, $parameters)
        {
            return preg_match('/^[\pL\pM\pN\/_-]+$/u', $value);
        });

        Validator::extend('valid_name', function ($attribute, $value, $parameters)
        {
            return (strip_tags($value) == $value);
        });

        return Validator::make($data, $rules, $messages, array(
            'link'  => __d('content', 'URL'),
            'name'  => __d('content', 'Name'),
            'local' => __d('content', 'Local')
        ));
    }

    public function index($id)
    {
        $authUser = Auth::user();

        try {
            $menu = Menu::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->with('danger', __d('content', 'Menu not found: #{0}', $id));
        }

        $posts = array_map(function ($type) use ($menu)
        {
            return $this->generatePostsForm($type, $menu);

        }, PostType::get(function ($type)
        {
            return $type->showInNavMenus();

        }));

        $taxonomies = array_map(function ($type) use ($menu)
        {
            return $this->generateTaxonomiesForm($type, $menu);

        }, TaxonomyType::get(function ($type)
        {
            return $type->showInNavMenus();

        }));

        return $this->createView()
            ->shares('title', __d('content', 'Manage a Menu'))
            ->with('menu', $menu)
            ->with('blocks', array_merge($posts, $taxonomies));
    }

    public function store(Request $request, $id, $mode)
    {
        try {
            $menu = Menu::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->with('danger', __d('content', 'Menu not found: #{0}', $id));
        }

        $result = $this->createMenuItems($request, $menu, $mode);

        if ($result instanceof SymfonyResponse) {
            return $result;
        }

        $menu->updateCount();

        // Invalidate the cached menu data.
        Cache::forget('content.menus.' .$menu->slug);

        return Redirect::back()->with('success', __d('content', 'The Menu Item(s) was successfully created.'));
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

        Validator::extend('valid_name', function ($attribute, $value, $parameters)
        {
            return (strip_tags($value) == $value);
        });

        $validator = Validator::make($input = $request->all(),
            array('name'       => 'required|valid_name'),
            array('valid_name' => __d('content', 'The :attribute field is not a valid name.')),
            array('name'       => __d('content', 'Name'))
        );

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator->errors());
        }

        $item->title = Arr::get($input, 'name');

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

    protected function createMenuItems(Request $request, Menu $menu, $mode)
    {
        $authUser = Auth::user();

        if ($mode == 'posts') {
            return $this->createPostLinks($request, $taxonomy, $authUser);
        }

        //
        else if ($mode == 'taxonomies') {
            return $this->createTaxonomyLinks($request, $taxonomy, $authUser);
        }

        //
        // Handle the custom links.

        else if ($mode != 'custom') {
            return Redirect::back()->with('danger', __d('content', 'Invalid storing mode [{0}]', $mode));
        }

        $validator = $this->validator(
            $input = $request->all(), $local = $request->has('local')
        );

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator->errors());
        }

        $name = Arr::get($input, 'name');

        $url = Arr::get($input, 'link');

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
            'menu_item_object'           => 'custom',
            'menu_item_object_id'        => $post->id,
            'menu_item_target'           => null,
            'menu_item_url'              => $local ? site_url($url) : $url,
        ));

        $menuLink->taxonomies()->attach($menu);
    }

    protected function createPostLinks(Request $request, Menu $menu, User $authUser)
    {
        $rules = array(
            // The type contains a Post type.
            'type'  => 'required|in:' . implode(',', PostType::getNames()),

            // The items[] should contain an array of valid Post IDs.
            'items' => 'required|array|exists:posts,id',
        );

        $validator = Validator::make($input = $request->all(), $rules, array(), array(
            'type'  => __d('content', 'Post Type'),
            'items' => __d('content', 'Posts')
        ));

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator->errors());
        }

        $type = Arr::get($input, 'type', 'post');

        $items = Arr::get($input, 'items', array());

        //
        $posts = Post::where('type', $type)->whereIn('id', $items);

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
        $rules = array(
            // The type contains a Taxonomy type.
            'type'  => 'required|in:' . implode(',', TaxonomyType::getNames()),

            // The items[] contains an array of Taxonomies IDs.
            'items' => 'required|array|exists:taxonomies,id',
        );

        $validator = Validator::make($input = $request->all(), $rules, array(), array(
            'type'  => __d('content', 'Taxonomy Type'),
            'items' => __d('content', 'Taxonomies')
        ));

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator->errors());
        }

        $type = Arr::get($input, 'type', 'category');

        $items = Arr::get($input, 'items', array());

        //
        $taxonomies = Taxonomy::where('taxonomy', $type)->whereIn('id', $items);

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

    protected function generatePostsForm(ContentType $postType, Menu $menu)
    {
        $type = $postType->name();

        $posts = Post::where('type', $type)->where('parent_id', 0)->whereIn('status', array('publish', 'password'))->get();

        $items = $this->generatePostsListing($type, $posts);

        //
        $data = compact('menu', 'type', 'items', 'postType');

        return View::make('Modules/Content::Partials/Admin/MenuItems/PostsForm', $data)->render();
    }

    protected function generateTaxonomiesForm(ContentType $taxonomyType, Menu $menu)
    {
        $type = $taxonomyType->name();

        $taxonomies = Taxonomy::where('taxonomy', $type)->where('parent_id', 0)->get();

        $items = $this->generateTaxonomiesListing($type, $taxonomies);

        //
        $data = compact('menu', 'type', 'items', 'taxonomyType');

        return View::make('Modules/Content::Partials/Admin/MenuItems/TaxonomiesForm', $data)->render();
    }

    protected function generatePostsListing($type, Collection $posts, $level = 0)
    {
        $results = array_map(function ($post) use ($type, $level)
        {
            $data = compact('type', 'post', 'level');

            $result = View::make('Modules/Content::Partials/Admin/MenuItems/PostCheckBox', $data)->render();

            // Process the children.
            $children = $post->children()->where('type', $type)->whereIn('status', array('publish', 'password'))->get();

            if (! $children->isEmpty()) {
                $result .= $this->generatePostsListing($type, $children, $level + 1);
            }

            return $result;

        }, $posts->all());

        return implode("\n", $results);
    }

    protected function generateTaxonomiesListing($type, Collection $taxonomies, $level = 0)
    {
        $results = array_map(function ($taxonomy) use ($type, $level)
        {
            $data = compact('type', 'taxonomy', 'level');

            $result = View::make('Modules/Content::Partials/Admin/MenuItems/TaxonomyCheckBox', $data)->render();

            // Process the children.
            $children = $taxonomy->children()->where('taxonomy', $type)->get();

            if (! $children->isEmpty()) {
                $result .= $this->generateTaxonomiesListing($type, $children, $level + 1);
            }

            return $result;

        }, $taxonomies->all());

        return implode("\n", $results);
    }
}
