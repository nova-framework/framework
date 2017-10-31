<?php

namespace App\Modules\Attachments\Controllers;

use Nova\Http\UploadedFile;
use Nova\Http\Request;
use Nova\Database\ORM\ModelNotFoundException;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\File;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\Response;

use App\Modules\Attachments\Controllers\FileServer as BaseController;
use App\Modules\Attachments\Models\Attachment;


class Attachments extends BaseController
{

    public function serve(Request $request, $method, $token, $fileName)
    {
        $disposition = ($method == 'download') ? 'attachment' : 'inline';

        return $this->serveFile($token, $fileName, $request, $disposition);
    }

    public function store(Request $request)
    {
        $file = $request->file('file');

        if (! $request->has('chunks')) {
            return $this->handleUploadedFile($file, $request);
        }

        $uuid = $request->input('uuid');

        // Get the temporary file path for this UUID.
        $filePath = $this->getFilePath($uuid);

        // Create the temporary file's directory.
        File::makeDirectory(dirname($filePath), 0755, true, true);

        // Store the chunk data in the temporary file.
        $tempPath = $file->getRealPath();

        if (file_put_contents($filePath, file_get_contents($tempPath), FILE_APPEND) === false) {
            return Response::json(array('error' => 'Chunk could not be saved.'), 400);
        }

        return Response::json(array('success' => true), 200);
    }

    public function done(Request $request)
    {
        $uuid = $request->input('uuid');

        if (! is_readable($filePath = $this->getFilePath($uuid))) {
            return Response::json(array('error' => 'Temporary file not found.'), 400);
        }

        $fileSize = $request->input('size');

        if ($fileSize != filesize($filePath)) {
            return Response::json(array('error' => 'Invalid temporary file.'), 400);
        }

        $fileName = $request->input('name');
        $mimeType = $request->input('type');

        // Create an UploadedFile instance from the temporary file.
        $file = new UploadedFile($filePath, $fileName, $mimeType, $fileSize, UPLOAD_ERR_OK, true);

        return $this->handleUploadedFile($file, $request, $filePath);
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

        if (! is_null($filePath) && file_exists($filePath)) {
            @unlink($filePath);
        }

        $data = array(
            'id'       => $attachment->id,
            'url'      => $attachment->url(),
            'download' => $attachment->url(true),
            'status'   => 'success',
        );

        return Response::json($data, 200);
    }

    protected function getFilePath($name)
    {
        return storage_path('upload') .DS .sha1($name) .'.part';
    }
}
