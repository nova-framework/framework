<?php

namespace App\Modules\Content\Controllers\Admin;

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Http\Request;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Response;
use Nova\Support\Facades\Validator;
use Nova\Support\Str;

use App\Modules\Content\Models\Taxonomy;
use App\Modules\Content\Models\Term;
use App\Modules\Platform\Controllers\Admin\BaseController;


class Taxonomies extends BaseController
{

    protected function validator(array $data, $id = null)
    {
        $ignore = ! is_null($id) ? ',' .intval($id) : '';

        // The Validation rules.
        $rules = array(
            'name'           => 'required|min:3|max:255|valid_text',
            'slug'           => 'min:4|max:100|alpha_dash|unique:terms,slug' .$ignore,
            'description'    => 'min:3|max:1000|valid_text',
            'taxonomy'       => 'required|in:category,post_tag',
        );

        $messages = array(
            'valid_name'      => __d('users', 'The :attribute field is not a valid name.'),
            'strong_password' => __d('users', 'The :attribute field is not strong enough.'),
        );

        $attributes = array(
            'name'        => __d('users', 'Name'),
            'slug'        => __d('users', 'Slug'),
            'description' => __d('users', 'E-Description'),
        );

        // Add the custom Validation Rule commands.
        Validator::extend('valid_text', function($attribute, $value, $parameters)
        {
            return strip_tags($value) == $value;
        });

        return Validator::make($data, $rules, $messages, $attributes);
    }

    public function index()
    {
        $type = 'category';

        $name  = __d('content', 'Category');
        $title = __d('content', 'Categories');

        $items = Taxonomy::where('taxonomy', $type)->paginate(15);

        $categories = $this->generateCategoriesSelect();

        return $this->createView()
            ->shares('title', $title)
            ->with(compact('items', 'type', 'name', 'title', 'categories'));
    }

    public function tags()
    {
        $type = 'tag';

        $name  = __d('content', 'Tag');
        $title = __d('content', 'Tags');

        $items = Taxonomy::where('taxonomy', 'post_tag')->paginate(15);

        return $this->createView(compact('items', 'type', 'name'), 'Index')
            ->shares('title', $title)
            ->with('categories', '');
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

            return Redirect::back()->withInput()->withStatus($validator->errors(), 'danger');
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
            'taxonomy'    => $input['taxonomy'],
            'description' => $input['description'],
            'parent_id'   => $parentId,
        ));

        if ($request->ajax() || $request->wantsJson()) {
            // The request was made by the Post Editor via AJAX, so we will return a fresh categories select.
            return Response::json(array(
                'categoryId' => $taxonomy->id,
                'categories' => $this->generateCategoriesSelect()

            ), 200);
        }

        $type = $taxonomy->taxonomy == 'post_tag' ? 'tag' : $taxonomy->taxonomy;

        $name = Config::get("content::labels.{$type}.name", Str::title($type));

        return Redirect::back()
            ->withStatus(__d('users', 'The {0} <b>{1}</b> was successfully created.', $name, $input['name']), 'success');
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();

        try {
            $taxonomy = Taxonomy::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->withStatus(__d('users', 'Field not found: #{0}', $id), 'danger');
        }

        $term = $taxonomy->term;

        // Validate the Input data.
        $validator = $this->validator($input, $term->id);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withStatus($validator->errors(), 'danger');
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

        //
        $type = $taxonomy->taxonomy == 'post_tag' ? 'tag' : $taxonomy->taxonomy;

        $name = Config::get("content::labels.{$type}.name", Str::title($type));

        return Redirect::back()
            ->withStatus(__d('users', 'The {0} <b>{1}</b> was successfully updated.', $name, $input['name']), 'success');
    }

    public function destroy($id)
    {
        try {
            $taxonomy = Taxonomy::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->withStatus(__d('users', 'Field not found: #{0}', $id), 'danger');
        }

        $taxonomy->children->each(function ($child) use ($taxonomy)
        {
            $child->parent_id = $taxonomy->parent_id;

            $child->save();
        });

        $taxonomy->term->delete();

        $taxonomy->delete();

        //
        $type = $taxonomy->taxonomy == 'post_tag' ? 'tag' : $taxonomy->taxonomy;

        $name = Config::get("content::labels.{$type}.name", Str::title($type));

        return Redirect::back()
            ->withStatus(__d('users', 'The {0} <b>{1}</b> was successfully deleted.', $name, $taxonomy->name), 'success');
    }

    public function categories($id, $parent)
    {
        $category = Taxonomy::findOrFail($id);

        $result = $this->generateCategoriesSelect($category->id, $parent);

        return Response::make($result, 200);
    }

    protected function generateCategoriesSelect($categoryId = 0, $parentId = 0, $categories = null, $level = 0)
    {
        $result = '';

        if (is_null($categories)) {
            $categories = Taxonomy::with('children')->where('taxonomy', 'category')->where('parent_id', 0)->get();

            $result = '<option value="0">' .__d('content', 'None') .'</option>' ."\n";
        }

        foreach ($categories as $category) {
            if ($category->id == $categoryId) {
                continue;
            }

            $result .= '<option value="' .$category->id .'"' .($category->id == $parentId ? ' selected="selected"' : '') .'>' .trim(str_repeat('--', $level) .' ' .$category->name) .'</option>' ."\n";

            // Process the children.
            $category->load('children');

            if (! $category->children->isEmpty()) {
                $level++;

                $result .= $this->generateCategoriesSelect($categoryId, $parentId, $category->children, $level);
            }
        }

        return $result;
    }
}
