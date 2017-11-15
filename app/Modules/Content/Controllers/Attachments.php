<?php

namespace App\Modules\Content\Controllers;

use Nova\Http\Request;
use Nova\Routing\Controller as BaseController;
use Nova\Support\Facades\App;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\File;
use Nova\Support\Facades\Response;
use Nova\Support\Str;

use App\Modules\Content\Models\Attachment;

use Intervention\Image\ImageManagerStatic as Image;


class Attachments extends BaseController
{

    public function serve(Request $request, $name)
    {
        $upload = Attachment::where("name", $name)->firstOrFail();

        //
        $basePath = Config::get('content::attachments.path', base_path('assets/files'));

        $path = $basePath .DS .$name;

        if (! File::exists($path)) {
            abort(404);
        }

        // Check if Thumbnail
        $size = $request->input('s');

        if (isset($size) && Str::is('image/*', $upload->mime_type)) {
            $thumbPath = Config::get('content::attachments.thumbPath', base_path('assets/files/thumbnails'));

            if (! File::exists($thumbPath)) {
                File::makeDirectory($thumbPath, 0755, true, true);
            }
            if (! is_numeric($size)) {
                $size = 150;
            }

            $name = pathinfo($upload->name, PATHINFO_FILENAME);

            $extension = pathinfo($upload->name, PATHINFO_EXTENSION);

            $filePath = $thumbPath .DS .$name .'-' .$size ."x" .$size .'.' .$extension;

            if (! File::exists($filePath)) {
                $image = Image::make($path);

                $image->fit($size, $size, function ($constraint)
                {
                    $constraint->aspectRatio();
                });

                $image->save($filePath);
            }

            $path = $filePath;
        }

        $download = $request->input('download');

        $disposition = isset($download) ? 'attachment' : 'inline';

        // Create a Assets Dispatcher instance.
        $dispatcher = App::make('assets.dispatcher');

        return $dispatcher->serve($path, $request, $disposition, $upload->title);
    }
}
