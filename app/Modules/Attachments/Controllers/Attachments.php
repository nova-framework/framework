<?php

namespace App\Modules\Attachments\Controllers;

use Nova\Container\Container;
use Nova\Http\UploadedFile;
use Nova\Http\Request;
use Nova\Database\ORM\ModelNotFoundException;
use Nova\Filesystem\Filesystem;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\Response;

use App\Modules\Attachments\Controllers\BaseController;
use App\Modules\Attachments\Models\Attachment;


class Attachments extends BaseController
{
    /**
     * The Filesystem instance.
     *
     * @var \Nova\Filesystem\Filesystem
     */
    protected $files;


    public function __construct(Container $container, Filesystem $files)
    {
        parent::__construct($container);

        //
        $this->files = $files;
    }


    public function serve(Request $request, $method, $token, $fileName)
    {
        $disposition = ($method == 'download') ? 'attachment' : 'inline';

        return $this->serveFile($token, $fileName, $request, $disposition);
    }

    public function store(Request $request)
    {
        if (! $request->hasFile('file')) {
            return Response::json(array('error' => 'Invalid request'), 400);
        }

        $file = $request->file('file');

        if (! $request->has('chunks')) {
            return $this->handleUploadedFile($file, $request);
        }

        // Get the temporary file path.
        $filePath = $this->getFilePath($request);

        // Store the received chunk data in the temporary file.
        $this->ensureDirectoryExists($filePath);

        if ($this->files->append($filePath, $this->files->get($file->path())) === false) {
            return Response::json(array('error' => 'Chunk could not be saved'), 400);
        }

        return Response::json(array('success' => true), 200);
    }

    public function done(Request $request)
    {
        if (! $request->has('uuid') || ! $request->has('name')) {
            return Response::json(array('error' => 'Invalid request'), 400);
        }

        $originalName = $request->input('name');

        $mimeType = $request->input('type');

        $size = $request->input('size');

        // Get the temporary file path.
        $path = $this->getFilePath($request);

        if ($this->files->exists($path) && ($this->files->size($path) === (int) $size)) {
            $file = new UploadedFile($path, $originalName, $mimeType, $size, UPLOAD_ERR_OK, true);

            return $this->handleUploadedFile($file, $request, $path);
        }

        return Response::json(array('error' => 'Invalid temporary file'), 400);
    }

    public function destroy($id)
    {
        try {
            $attachment = Attachment::findOrFail($id);

            $attachment->delete();
        }
        catch (ModelNotFoundException $e) {
            return Response::json(array('error' => 'Attachment not found: #' .$id), 400);
        }

        return Response::json(array('success' => true), 200);
    }

    protected function handleUploadedFile(UploadedFile $file, Request $request, $path = null)
    {
        $guard = $request->input('auth_guard');

        if (is_null($user = Auth::guard($guard)->user())) {
            return Response::json(array('error' => 'Invalid ownership'), 400);
        }

        $attachment = Attachment::create(array(
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'type' => $file->getClientMimeType(),

            // The inner FileField use the UploadedFile instance.
            'file' => $file,

            // Fill the 'ownerable' morph.
            'ownerable_id'   => $user->id,
            'ownerable_type' => $this->getAuthProviderModel($guard),

            // Fill the 'attachable' morph with dummy values.
            'attachable_id'   => 0,
            'attachable_type' => '',
        ));

        if (! is_null($path) && $this->files->exists($path)) {
            $this->files->delete($path);
        }

        $data = array(
            'id'  => $attachment->id,
            'url' => $attachment->url(),

            // The download URL of this file.
            'download' => $attachment->url(true),
        );

        return Response::json($data, 200);
    }

    protected function ensureDirectoryExists($path)
    {
        if (! $this->files->exists($directory = dirname($path))) {
            $this->files->makeDirectory($directory, 0755, true, true);
        }
    }

    protected function getFilePath(Request $request)
    {
        $uuid = $request->input('uuid');

        return storage_path('upload') .DS .sha1($uuid) .'.part';
    }

    protected function getAuthProviderModel($guard)
    {
        if (is_null($guard) || (! empty($guard) && ! Config::has('auth.guards.' .$guard))) {
            $guard = Config::get('auth.defaults.guard', 'web');
        }

        $provider = Config::get("auth.guards.{$guard}.provider");

        return Config::get("auth.providers.{$provider}.model");
    }
}
