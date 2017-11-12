<?php

namespace App\Modules\Content\Controllers\Admin;

use Nova\Http\Request;

use App\Modules\Platform\Controllers\Admin\BaseController;


class Menus extends BaseController
{

    public function index()
    {
        return $this->createView()
            ->shares('title', __d('content', 'Menus'));
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
}
