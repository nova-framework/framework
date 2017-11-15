<?php

namespace App\Modules\Content\Controllers\Admin;

use Nova\Http\Request;
use Nova\Support\Facades\App;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\File;
use Nova\Support\Facades\Response;
use Nova\Support\Str;

use App\Modules\Content\Models\Attachment;
use App\Modules\Platform\Controllers\Admin\BaseController;

use Intervention\Image\ImageManagerStatic as Image;


class Attachments extends BaseController
{

    public function index()
    {
        return $this->createView()
            ->shares('title', __d('content', 'Attachments'));
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

    public function serve(Request $request, $slug)
    {
        $upload = Attachment::where("name", $slug)->firstOrFail();

        $path = ROOTDIR .'assets' .DS .'files' .DS .$upload->name;

        if (! File::exists($path)) {
            abort(404);
        }

        // Check if Thumbnail
        $size = $request->input('s');

        if (isset($size)) {
            if(! is_numeric($size)) {
                $size = 150;
            }

            $thumbPath = storage_path("files/" .$size ."x" .$size .'_' .$upload->name);

            if (! File::exists($thumbPath)) {
                $image = Image::make($path);

                $image->fit($size, $size, function ($constraint)
                {
                    $constraint->aspectRatio();
                });

                $image->save($thumbPath);
            }

            $path = $thumbPath;
        }

        $fileDispatcher = App::make('assets.dispatcher');

        return $fileDispatcher->serve($path, $request);
    }

    public function upload(Request $request)
    {
        $authUser = Auth::user();

        if (! $request->hasFile('file')) {
            return Response::json('error: upload file not found.', 400);
        }

        $file = $request->file('file');

        //
        $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $fileName = sprintf('%s-%s.%s',
            uniqid(), Str::slug($fileName), $file->getClientOriginalExtension()
        );

        //
        $folder = base_path('assets/files');

        if (! $file->move($folder, $fileName)) {
            return Response::json(array('status' => 'error'), 400);
        }

        $upload = Attachment::create(array(
            'author_id' => $authUser->id,
            'type'      => 'attachment',
            'title'     => $file->getClientOriginalName(),
            'name'      => $fileName,
            'guid'      => site_url('assets/files/' .$fileName),
            'mime_type' => $file->getClientMimeType(),
        ));

        // Handle the MetaData.
        $upload->meta->attachment_image_alt = '';

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
