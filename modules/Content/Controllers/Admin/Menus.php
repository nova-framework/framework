<?php

namespace Modules\Content\Controllers\Admin;

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Http\Request;
use Nova\Support\Facades\Cache;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Response;
use Nova\Support\Facades\Validator;

use Modules\Content\Models\Menu;
use Modules\Content\Models\Taxonomy;
use Modules\Content\Models\Term;
use Modules\Platform\Controllers\Admin\BaseController;


class Menus extends BaseController
{

    protected function validator(Request $request)
    {
        $rules = array(
            'name'        => 'required|valid_text',
            'description' => 'required|valid_text',
        );

        $messages = array(
            'valid_text' => __d('content', 'The :attribute field is not a valid text.'),
        );

        $attributes = array(
            'name'        => __d('content', 'Name'),
            'description' => __d('content', 'Description'),
        );

        // Add the custom Validation Rule commands.
        Validator::extend('valid_text', function ($attribute, $value, $parameters)
        {
            return (strip_tags($value) == $value);
        });

        return Validator::make($request->all(), $rules, $messages, $attributes);
    }

    public function index()
    {
        $menus = Menu::paginate(15);

        return $this->createView()
            ->shares('title', __d('content', 'Menus'))
            ->with('menus', $menus);
    }

    public function store(Request $request)
    {
        $validator = $this->validator($request);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator->errors());
        }

        $name = $request->input('name');

        $slug = Term::uniqueSlug($name, 'nav_menu');

        $term = Term::create(array(
            'name'   => $name,
            'slug'   => $slug,
            'group'  => 0,
        ));

        $taxonomy = Taxonomy::create(array(
            'term_id'     => $term->id,
            'taxonomy'    => 'nav_menu',
            'description' => $request->input('description'),
            'parent_id'   => 0,
            'count'       => 0,
        ));

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

        $validator = $this->validator($request);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator->errors());
        }

        $term = $menu->term()->first();

        // Get the original information of the Term.
        $original = $term->name;

        $slug = $term->slug;

        // Update the Term.
        $term->name = $name = $request->input('name');

        $term->slug = Term::uniqueSlug($name, 'nav_menu', $menu->id);

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
