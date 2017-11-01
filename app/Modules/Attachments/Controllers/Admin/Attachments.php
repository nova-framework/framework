<?php

namespace App\Modules\Attachments\Controllers\Admin;

use Nova\Http\Request;
use Nova\Support\Facades\Auth;

use App\Modules\Platform\Controllers\Admin\BaseController;


class Attachments extends BaseController
{

    public function index()
    {
        $authUser = Auth::user();

        // Prepare the existing files information.
        $attachments = $authUser->attachments->map(function ($attachment)
        {
            return array(
                'id'       => $attachment->id,
                'name'     => $attachment->name,
                'size'     => $attachment->size,
                'type'     => $attachment->type,
                'url'      => $attachment->url(),

                // The download URL.
                'download' => $attachment->url(true),
            );
        });

        $attachments = array(
            'files'  => $attachments->toArray(),

            // The owner information.
            'authId'    => $authUser->id,
            'authGuard' => 'web',

            // The Attachable.
            'attachable' => null,

            // Rendering options.
            'downloadable' => true,
            'deletable'    => true,

            // Limits.
            'maxFiles'    => 1000,
            'maxFilesize' => 1000, // 1GB
        );

        return $this->createView()
            ->shares('title', __d('attachments', 'Attachments'))
            ->with('attachments', $attachments);
    }

    public function update(Request $request)
    {
        dump($request->all());
    }
}
