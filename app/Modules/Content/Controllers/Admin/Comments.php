<?php

namespace App\Modules\Content\Controllers\Admin;

use Nova\Http\Request;

use App\Modules\Platform\Controllers\Admin\BaseController;


class Comments extends BaseController
{

    public function index()
    {
        return $this->createView()
            ->shares('title', __d('content', 'Comments'));
    }

    public function edit()
    {
        return $this->createView()
            ->shares('title', __d('content', 'Edit Comment'));
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
    }
}
