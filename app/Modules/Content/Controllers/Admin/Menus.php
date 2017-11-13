<?php

namespace App\Modules\Content\Controllers\Admin;

use Nova\Http\Request;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\Event;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Response;

use App\Modules\Content\Models\Menu;
use App\Modules\Content\Models\MenuItem;
use App\Modules\Content\Models\Post;
use App\Modules\Content\Models\Taxonomy;
use App\Modules\Platform\Controllers\Admin\BaseController;


class Menus extends BaseController
{

    public function index()
    {
        $menus = Menu::paginate(15);

        return $this->createView()
            ->shares('title', __d('content', 'Menus'))
            ->with('menus', $menus);
    }

    public function edit()
    {
        return $this->createView()
            ->shares('title', __d('content', 'Edit Menu'));
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
    }

    public function items($id)
    {
        $authUser = Auth::user();

        try {
            $menu = Menu::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->withStatus(__d('content', 'Menu not found: #{0}', $id), 'danger');
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

    public function order(Request $request, $id)
    {
        try {
            $vocabulary = Menu::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Response::json(array('error' => 'Not Found'), 400);
        }

        $items = json_decode(
            $request->get('json')
        );

        $this->updateMenuItemsOrder($items);

        return Response::json(array('success' => true), 200);
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

            if (! empty($item->children)) {
                $this->updateMenuItemsOrder($item->children, $term->id);
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

            $result .= '<div class="checkbox" style="padding-left: ' .(($level > 0) ? ($level * 25) .'px' : '') .'"><label><input class="category-checkbox" name="category[]" value="' .$post->id .'" type="checkbox">&nbsp;&nbsp;' .$post->title .'</label></div>';

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
