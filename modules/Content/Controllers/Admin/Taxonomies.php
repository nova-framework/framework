<?php

namespace Modules\Content\Controllers\Admin;

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Http\Request;
use Nova\Support\Facades\Cache;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Response;
use Nova\Support\Facades\Validator;
use Nova\Support\Str;

use Modules\Content\Models\Taxonomy;
use Modules\Content\Models\Term;
use Modules\Content\Support\Facades\TaxonomyType;
use Modules\Platform\Controllers\Admin\BaseController;


class Taxonomies extends BaseController
{

    protected function validator(array $data, $id = null)
    {
        $taxonomies = TaxonomyType::getNames();

        //
        $ignore = ! is_null($id) ? ',' .intval($id) : '';

        // The Validation rules.
        $rules = array(
            'name'           => 'required|min:3|max:255|valid_text',
            'slug'           => 'min:4|max:100|alpha_dash|unique:terms,slug' .$ignore,
            'description'    => 'min:3|max:1000|valid_text',
            'taxonomy'       => 'required|in:' .implode(',', $taxonomies),
        );

        $messages = array(
            'valid_name'      => __d('content', 'The :attribute field is not a valid name.'),
            'strong_password' => __d('content', 'The :attribute field is not strong enough.'),
        );

        $attributes = array(
            'name'        => __d('content', 'Name'),
            'slug'        => __d('content', 'Slug'),
            'description' => __d('content', 'E-Description'),
        );

        // Add the custom Validation Rule commands.
        Validator::extend('valid_text', function($attribute, $value, $parameters)
        {
            return strip_tags($value) == $value;
        });

        return Validator::make($data, $rules, $messages, $attributes);
    }

    public function index(Request $request, $slug)
    {
        $taxonomyType = TaxonomyType::findBySlug($slug);

        $type = $taxonomyType->name();

        //
        $name = $taxonomyType->label('name');

        $items = Taxonomy::where('taxonomy', $type)->paginate(15);

        if ($taxonomyType->isHierarchical()) {
            $taxonomies = $this->generateTaxonomiesSelect($type);
        } else {
            $taxonomies = '';
        }

        return $this->createView()
            ->shares('title', $taxonomyType->label('title'))
            ->with(compact('items', 'type', 'name', 'taxonomyType', 'taxonomies'));
    }

    public function store(Request $request)
    {
        $input = $request->all();

        // Validate the Input data.
        $validator = $this->validator($input);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                // The request was made by the Post Editor via AJAX.
                return Response::json(array('error' => $validator->errors()), 400);
            }

            return Redirect::back()->withInput()->withErrors($validator->errors());
        }

        $slug = ! empty($input['slug']) ? $input['slug'] : Term::uniqueSlug($input['name'], $input['taxonomy']);

        $parentId = ! empty($input['parent']) ? (int) $input['parent'] : 0;

        // Create the Term.
        $term = Term::create(array(
            'name' => $input['name'],
            'slug' => $slug,
        ));

        // Create the Taxonomy.
        $taxonomy = Taxonomy::create(array(
            'term_id'     => $term->id,
            'taxonomy'    => $type = $input['taxonomy'],
            'description' => $input['description'],
            'parent_id'   => $parentId,
        ));

        if ($request->ajax() || $request->wantsJson()) {
            // The request was made by the Post Editor via AJAX, so we will return a fresh categories select.
            $taxonomies = $request->input('taxonomy', array());

            // Add also the fresh category ID.
            $taxonomies[] = $taxonomy->id;

            return Response::json(array(
                'taxonomyId' => $taxonomy->id,
                'taxonomies' => $this->generateTaxonomiesCheckBox($type, $taxonomies)

            ), 200);
        }

        // Invalidate the content caches.
        $this->clearContentCache($type);

        //
        $taxonomyType = TaxonomyType::make($type);

        $name = $taxonomyType->label('name');

        return Redirect::back()
            ->with('success', __d('content', 'The {0} <b>{1}</b> was successfully created.', $name, $input['name']));
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();

        try {
            $taxonomy = Taxonomy::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->with('danger', __d('content', 'Taxonomy not found: #{0}', $id));
        }

        $term = $taxonomy->term;

        // Validate the Input data.
        $validator = $this->validator($input, $term->id);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator->errors());
        }

        $slug = ! empty($input['slug']) ? $input['slug'] : Term::uniqueSlug($input['name'], $input['taxonomy']);

        $parentId = ! empty($input['parent']) ? (int) $input['parent'] : 0;

        // Update the Taxonomy.
        $taxonomy->description = $input['description'];
        $taxonomy->parent_id   = $parentId;

        $taxonomy->save();

        // Update the Term.
        $term->name = $input['name'];
        $term->slug = $slug;

        $term->save();

        // Invalidate the content caches.
        $this->clearContentCache($type = $taxonomy->taxonomy);

        //
        $taxonomyType = TaxonomyType::make($type);

        $name = $taxonomyType->label('name');

        return Redirect::back()
            ->with('success', __d('content', 'The {0} <b>{1}</b> was successfully updated.', $name, $input['name']));
    }

    public function destroy($id)
    {
        try {
            $taxonomy = Taxonomy::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->with('danger', __d('content', 'Taxonomy not found: #{0}', $id));
        }

        $taxonomy->children->each(function ($child) use ($taxonomy)
        {
            $child->parent_id = $taxonomy->parent_id;

            $child->save();
        });

        $taxonomy->term->delete();

        $taxonomy->delete();

        // Invalidate the content caches.
        $this->clearContentCache($type = $taxonomy->taxonomy);

        //
        $taxonomyType = TaxonomyType::make($type);

        $name = $taxonomyType->label('name');

        return Redirect::back()
            ->with('success', __d('content', 'The {0} <b>{1}</b> was successfully deleted.', $name, $taxonomy->name));
    }

    public function data($id, $parentId)
    {
        $taxonomy = Taxonomy::findOrFail($id);

        $result = $this->generateTaxonomiesSelect($taxonomy->taxonomy, $taxonomy->id, $parentId);

        return Response::make($result, 200);
    }

    protected function generateTaxonomiesCheckBox($type, array $selected = array(), $taxonomies = null, $level = 0)
    {
        $result = '';

        if (is_null($taxonomies)) {
            $taxonomies = Taxonomy::with('children')->where('taxonomy', $type)->where('parent_id', 0)->get();
        }

        foreach ($taxonomies as $taxonomy) {
            $result .= '<div class="checkbox"><label><input class="' .$type .'-checkbox" name="taxonomy[]" value="' .$taxonomy->id .'" type="checkbox" ' .(in_array($taxonomy->id, $selected) ? ' checked="checked"' : '') .'> ' .trim(str_repeat('--', $level) .' ' .$taxonomy->name) .'</label></div>';

            // Process the children.
            $taxonomy->load('children');

            if (! $taxonomy->children->isEmpty()) {
                $level++;

                $result .= $this->generateTaxonomiesCheckBox($type, $selected, $taxonomy->children, $level);
            }
        }

        return $result;
    }

    protected function generateTaxonomiesSelect($type, $taxonomyId = 0, $parentId = 0, $taxonomies = null, $level = 0)
    {
        $result = '';

        if (is_null($taxonomies)) {
            $taxonomies = Taxonomy::with('children')->where('taxonomy', $type)->where('parent_id', 0)->get();

            $result = '<option value="0">' .__d('content', 'None') .'</option>' ."\n";
        }

        foreach ($taxonomies as $taxonomy) {
            if ($taxonomy->id == $taxonomyId) {
                continue;
            }

            $result .= '<option value="' .$taxonomy->id .'"' .($taxonomy->id == $parentId ? ' selected="selected"' : '') .'>' .trim(str_repeat('--', $level) .' ' .$taxonomy->name) .'</option>' ."\n";

            // Process the children.
            $taxonomy->load('children');

            if (! $taxonomy->children->isEmpty()) {
                $level++;

                $result .= $this->generateTaxonomiesSelect($type, $taxonomyId, $parentId, $taxonomy->children, $level);
            }
        }

        return $result;
    }

    protected function clearContentCache($type)
    {
        Cache::forget('content.taxonomies.' .$type);
    }
}
