<?php

namespace Routing;

use Http\Request;
use Http\Response;
use Helpers\Inflector;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;


class AssetFileDispatcher
{
    /**
     * Create a new controller dispatcher instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Dispatch/Serve a file
     * @return bool
     */
    public function dispatch(Request $request)
    {
        // For proper Assets serving, the file URI should be either of the following:
        //
        // /templates/default/assets/css/style.css
        // /modules/blog/assets/css/style.css
        // /assets/css/style.css

        $uri = $request->path();

        if (! in_array($request->method(), array('GET', 'HEAD'))) {
            // No allowed HTTP method on the Request.
            $path = null;
        } else if (preg_match('#^assets/(.*)$#i', $uri, $matches)) {
            $path = ROOTDIR .'assets' .DS .$matches[1];
        } else if (preg_match('#^(templates|modules)/([^/]+)/assets/([^/]+)/(.*)$#i', $uri, $matches)) {
            $module = Inflector::classify($matches[2]);

            if(strtolower($matches[1]) == 'modules') {
                // A Module Asset file.
                $path = $this->getModuleAssetPath($module, $matches[3], $matches[4]);
            } else {
                // A Template Asset file.
                $path = $this->getTemplateAssetPath($module, $matches[3], $matches[4]);
            }
        } else {
            // The URI is not a Asset File path.
            $path = null;
        }

        //
        // Serve the specified Asset File.
        if (! empty($path)) {
            $response = $this->serveFile($path);
        } else {
            $response = null;
        }

        if($response instanceof BinaryFileResponse) {
            $response->isNotModified($request);

            $response->prepare($request);
        }

        return $response;
    }

    /**
     * Get the path of a Asset file
     * @return string|null
     */
    protected function getModuleAssetPath($module, $folder, $path)
    {
        $basePath = APPDIR .str_replace('/', DS, "Modules/$module/Assets/");

        return $basePath .$folder .DS .$path;
    }

    /**
     * Get the path of a Asset file
     * @return string|null
     */
    protected function getTemplateAssetPath($template, $folder, $path)
    {
        $path = str_replace('/', DS, $path);

        // Retrieve the Template Info
        $infoFile = APPDIR .'Templates' .DS .$template .DS .'template.json';

        if (is_readable($infoFile)) {
            $info = json_decode(file_get_contents($infoFile), true);

            // Template Info should be always an array; ensure that.
            $info = $info ?: array();
        } else {
            $info = array();
        }

        //
        $basePath = null;

        // Get the current Asset Folder's Mode.
        $mode = array_get($info, 'assets.paths.' .$folder, 'local');

        if ($mode == 'local') {
            $basePath = APPDIR .str_replace('/', DS, "Templates/$template/Assets/");
        } else if ($mode == 'vendor') {
            // Get the Vendor name.
            $vendor = array_get($info, 'assets.vendor', '');

            if (! empty($vendor)) {
                $basePath = ROOTDIR .str_replace('/', DS, "vendor/$vendor/");
            }
        }

        return ! empty($basePath) ? $basePath .$folder .DS .$path : '';
    }

    /**
     * Serve a File.
     *
     * @param string $path
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function serveFile($path)
    {
        if (! file_exists($path)) {
            return new Response('', 404);
        } else if (! is_readable($path)) {
            return new Response('', 403);
        }

        // Collect the current file information.
        $guesser = MimeTypeGuesser::getInstance();

        // Even the Symfony's HTTP Foundation have troubles with the CSS and JS files?
        //
        // Hard coding the correct mime types for presently needed file extensions.
        switch ($fileExt = pathinfo($path, PATHINFO_EXTENSION)) {
            case 'css':
                $contentType = 'text/css';
                break;
            case 'js':
                $contentType = 'application/javascript';
                break;
            default:
                $contentType = $guesser->guess($path);
                break;
        }

        // Create a BinaryFileResponse instance.
        $response = new BinaryFileResponse($path, 200, array(), true, 'inline', true, false);

        // Set the Content type.
        $response->headers->set('Content-Type', $contentType);

        // Set the Cache Control.
        $response->setTtl(600);
        $response->setMaxAge(10800);
        $response->setSharedMaxAge(600);

        return $response;
    }

}
