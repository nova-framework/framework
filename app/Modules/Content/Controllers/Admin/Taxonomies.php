<?php

namespace App\Modules\Content\Controllers\Admin;

use Nova\Http\Request;

use App\Modules\Content\Models\Taxonomy;
use App\Modules\Platform\Controllers\Admin\BaseController;


class Taxonomies extends BaseController
{

    public function index()
    {
        $models = Taxonomy::with('term')->where('taxonomy', 'category')->paginate(15);

        $type = 'category';
        $name  = __d('content', 'Category');

        return $this->createView()
            ->shares('title', __d('content', 'Categories'))
            ->with(compact('models', 'type', 'name'));
    }

    public function tags()
    {
        $models = Taxonomy::with('term')->where('taxonomy', 'post_tag')->paginate(15);

        $type = 'tag';
        $name = __d('content', 'Tag');

        return $this->createView(compact('models', 'type', 'name'), 'Index')
            ->shares('title', __d('content', 'Tags'));
    }

    public function store(Request $request)
    {
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
    }
}
