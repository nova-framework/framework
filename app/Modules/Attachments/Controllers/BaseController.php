<?php

namespace App\Modules\Attachments\Controllers;

use Nova\Http\Request;
use Nova\Http\Response;
use Nova\Routing\Controller;
use Nova\Support\Facades\Config;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;

use Carbon\Carbon;


class BaseController extends Controller
{
    protected $filePath;


    /**
     * Call the parent construct
     */
    public function __construct()
    {
        $this->filePath = base_path('files') .str_replace('/', DS, '/attachments/');
    }

    /**
     * Serve a File.
     *
     * @param string $filePath
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function serveFile($fileName, Request $request, $disposition = 'inline')
    {
        $filePath = $this->filePath .$fileName;

        if (! file_exists($filePath)) {
            return new Response('File Not Found', 404);
        } else if (! is_readable($filePath)) {
            return new Response('Unauthorized Access', 403);
        }

        // Collect the current file information.
        $guesser = MimeTypeGuesser::getInstance();

        // Even the Symfony's HTTP Foundation have troubles with the CSS and JS files?
        //
        // Hard coding the correct mime types for presently needed file extensions.
        switch ($fileExt = pathinfo($filePath, PATHINFO_EXTENSION)) {
            case 'css':
                $contentType = 'text/css';
                break;
            case 'js':
                $contentType = 'application/javascript';
                break;
            default:
                $contentType = $guesser->guess($filePath);
                break;
        }

        // Create a BinaryFileResponse instance.
        $response = new BinaryFileResponse($filePath, 200, array(), true, $disposition, true, false);

        // Set the Content type.
        $response->headers->set('Content-Type', $contentType);

        // Set the Content Disposition.
        list($unique, $fileName) = explode('-', $fileName, 2);

        $response->setContentDisposition($disposition, $fileName);

        // Set the Caching.
        $response->setTtl(600);
        $response->setMaxAge(10800);
        $response->setSharedMaxAge(600);

        // Prepare against the Request instance.
        $response->isNotModified($request);

        return $response;
    }
}
