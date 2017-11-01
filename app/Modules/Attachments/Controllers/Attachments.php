<?php

namespace App\Modules\Attachments\Controllers;

use Nova\Container\Container;
use Nova\Http\UploadedFile;
use Nova\Http\Request;
use Nova\Database\ORM\ModelNotFoundException;
use Nova\Filesystem\Filesystem;
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

        if ($this->files->append($filePath, $this->files->get($file->getRealPath())) === false) {
            return Response::json(array('error' => 'Chunk could not be saved'), 400);
        }

        return Response::json(array('success' => true), 200);
    }

    public function done(Request $request)
    {
        if (! $request->has('uuid') || ! $request->input('name')) {
            return Response::json(array('error' => 'Invalid request'), 400);
        }

        $fileName = $request->input('name');
        $mimeType = $request->input('type');

        $fileSize = (int) $request->input('size');

        // Get the temporary file path.
        $filePath = $this->getFilePath($request);

        if ($this->files->exists($filePath) && ($fileSize == $this->files->size($filePath))) {
            $file = new UploadedFile($filePath, $fileName, $mimeType, $fileSize, UPLOAD_ERR_OK, true);

            return $this->handleUploadedFile($file, $request, $filePath);
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

    protected function handleUploadedFile(UploadedFile $file, Request $request, $filePath = null)
    {
        $ownerableId   = $request->input('owner_id');
        $ownerableType = $request->input('owner_type');

        $attachment = Attachment::create(array(
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'type' => $file->getClientMimeType(),

            // The inner FileField use the UploadedFile instance.
            'file' => $file,

            // Fill the 'ownerable' morph.
            'ownerable_id'   => $ownerableId,
            'ownerable_type' => $ownerableType,

            // Fill the 'attachable' morph with dummy values.
            'attachable_id'   => 0,
            'attachable_type' => '',
        ));

        if (! is_null($filePath) && $this->files->exists($filePath)) {
            $this->files->delete($filePath);
        }

        $data = array(
            'id'  => $attachment->id,
            'url' => $attachment->url(),

            // The download URL of this file.
            'download' => $attachment->url(true),
        );

        return Response::json($data, 200);
    }

    protected function getFilePath(Request $request)
    {
        $uuid = $request->input('uuid');

        return storage_path('upload') .DS .sha1($uuid) .'.part';
    }

    protected function ensureDirectoryExists($path)
    {
        return $this->files->makeDirectory(dirname($path), 0755, true, true);
    }
}
