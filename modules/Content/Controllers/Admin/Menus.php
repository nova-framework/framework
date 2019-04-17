<?php

namespace Modules\Content\Controllers\Admin;

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Http\Request;
use Nova\Support\Facades\Cache;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Response;

use Modules\Content\Models\Menu;
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
}
