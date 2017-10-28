<?php

namespace App\Modules\Attachments\Controllers\Admin;

use Nova\Http\Request;

use App\Modules\Attachments\Models\Attachment;
use App\Modules\Platform\Controllers\Admin\BaseController;


class Attachments extends BaseController
{

    public function index()
    {
        $models = Attachment::all();

        // Prepare the existing files information.
        $attachments = $models->map(function ($model)
        {
            return array(
                'id'       => $model->id,
                'name'     => $model->name,
                'size'     => $model->size,
                'url'      => $model->url(),
                'download' => $model->url(true),
            );

        })->toArray();

        return $this->createView()
            ->shares('title', __d('attachments', 'Attachments'))
            ->with('attachments', $attachments);
    }

    public function update(Request $request)
    {
        dump($request->all());
    }
}
