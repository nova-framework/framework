<?php

namespace Modules\Content\Controllers\Admin;

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Http\Request;
use Nova\Support\Facades\App;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\File;
use Nova\Support\Facades\Response;
use Nova\Support\Str;

use Modules\Content\Models\Attachment;
use Modules\Platform\Controllers\Admin\BaseController;

use Intervention\Image\ImageManagerStatic as Image;


class Attachments extends BaseController
{

    public function index()
    {
        return $this->createView()
            ->shares('title', __d('content', 'Media Library'));
    }

    public function update(Request $request, $field)
    {
        try {
            $upload = Attachment::where('id', (int) $request->input('file_id'))->firstOrFail();
        }
        catch (ModelNotFoundException $e) {
            return Response::json(array('status' => 'failure', 'message' => 'Upload not found'), 400);
        }

        if ($field === 'caption') {
            $upload->excerpt = $request->input('caption');
        } else if ($field === 'description') {
            $upload->content = $request->input('description');
        } else {
            return Response::json(array('status' => 'failure', 'message' => 'Invalid field specified'), 400);
        }

        $upload->save();

        return Response::json(array('status' => 'success'), 200);
    }

    public function destroy(Request $request)
    {
        try {
            $upload = Attachment::where('id', (int) $request->input('file_id'))->firstOrFail();
        }
        catch (ModelNotFoundException $e) {
            return Response::json(array('status' => 'failure', 'message' => 'Upload not found'), 400);
        }

        $upload->delete();

        return Response::json(array('status' => 'success'), 200);
    }

    public function upload(Request $request)
    {
        $authUser = Auth::user();

        if (! $request->hasFile('file')) {
            return Response::json('error: upload file not found.', 400);
        }

        $file = $request->file('file');

        //
        $path = Config::get('content::attachments.path', base_path('assets/files'));

        if (! File::exists($path)) {
            File::makeDirectory($path, 0755, true, true);
        }

        $originalName = $file->getClientOriginalName();

        $name = pathinfo($originalName, PATHINFO_FILENAME);

        $fileName = sprintf('%s-%s.%s',
            uniqid(), Str::slug($name), $extension = $file->clientExtension()
        );

        $filePath = $path .DS .$fileName;

        $fileSize = $file->getSize();

        if (! $file->move($path, $fileName)) {
            return Response::json(array('status' => 'error'), 400);
        }

        $upload = Attachment::create(array(
            'author_id' => $authUser->id,
            'type'      => 'attachment',
            'title'     => $originalName,
            'name'      => $fileName,
            'guid'      => site_url('assets/files/' .$fileName),
            'mime_type' => $file->getClientMimeType(),
        ));

        // Handle the MetaData.
        $upload->meta->attachment_image_extension = $extension;
        $upload->meta->attachment_image_path      = $filePath;
        $upload->meta->attachment_image_size      = $fileSize;
        $upload->meta->attachment_image_alt       = '';

        // For the Post thumbnail() relationship.
        $upload->meta->attachment_metadata = null;

        $upload->save();

        return Response::json(array(
            'status' => 'success',
            'upload' => $upload

        ), 200);
    }

    public function uploaded()
    {
        $uploads = Attachment::all();

        //
        $result = array();

        foreach ($uploads as $upload) {
            $u = (object) array();

            //
            $u->id          = $upload->id;
            $u->name        = $upload->name;
            $u->type        = $upload->mime_type;
            $u->title       = $upload->title;
            $u->description = $upload->content;
            $u->caption     = $upload->excerpt;
            $u->user        = $upload->author->username;

            // The file viewer needs the extension.
            $u->extension = pathinfo($upload->name, PATHINFO_EXTENSION);

            $result[] = $u;
        }

        return Response::json(array('uploads' => $result), 200);
    }
}
