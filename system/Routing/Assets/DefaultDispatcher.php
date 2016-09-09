<?php

namespace Routing\Assets;

use Config\Config;
use Http\Request;
use Http\Response;
use Routing\Assets\DispatcherInterface;
use Support\Str;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;


class DefaultDispatcher implements DispatcherInterface
{
    /**
     * Create a new Default Dispatcher instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Dispatch a Assets File Response.
     *
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function dispatch(Request $request)
    {
        // For proper Assets serving, the file URI should be either of the following:
        //
        // /templates/default/assets/css/style.css
        // /modules/blog/assets/css/style.css
        // /assets/css/style.css

        // Check the HTTP Method on the Request.
        if (! in_array($request->method(), array('GET', 'HEAD'))) {
            return null;
        }

        // Check the URI on the Request.
        $uri = $request->path();

        if (preg_match('#^assets/(.*)$#i', $uri, $matches)) {
            $path = ROOTDIR .'assets' .DS .$matches[1];
        } else if (preg_match('#^(templates|modules)/([^/]+)/assets/([^/]+)/(.*)$#i', $uri, $matches)) {
            $module = Str::studly($matches[2]);

            if(strtolower($matches[1]) == 'modules') {
                // A Module Asset file.
                $path = static::getModulePath($module, $matches[3], $matches[4]);
            } else {
                // A Template Asset file.
                $path = static::getTemplatePath($module, $matches[3], $matches[4]);
            }
        } else {
            // The URI is not a Asset File path.
            return null;
        }

        // Get the Response instance associated to the Asset File.
        $response = $this->serve($path, $request);

        // Prepare the Response instance.
        $response->prepare($request);

        return $response;
    }

    /**
     * Serve a File.
     *
     * @param string $path
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function serve($path, Request $request)
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
        $cacheTime = Config::get('routing.assets.cacheTime', 10800);

        $response->setTtl(600);
        $response->setMaxAge($cacheTime);
        $response->setSharedMaxAge(600);

        // Prepare against the Request instance.
        $response->isNotModified($request);

        return $response;
    }

    /**
     * Get the path of a Asset file
     * @return string|null
     */
    protected static function getModulePath($module, $folder, $path)
    {
        $basePath = APPDIR .str_replace('/', DS, "Modules/$module/Assets/");

        return $basePath .$folder .DS .$path;
    }

    /**
     * Get the path of a Asset file
     * @return string|null
     */
    protected static function getTemplatePath($template, $folder, $path)
    {
        $path = str_replace('/', DS, $path);

        // Retrieve the Template Info
        $info = static::getTemplateInfo($template);

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
     * Get the Template Info
     * @return array
     */
    protected static function getTemplateInfo($template)
    {
        // Retrieve the Template Info
        $filePath = APPDIR .'Templates' .DS .$template .DS .'template.json';

        if (! is_readable($filePath)) {
            return array();
        }

        // Get the file contents and decode the JSON content.
        $result = json_decode(file_get_contents($filePath), true);

        // The Template Info should be always an array; ensure that.
        return $result ?: array();
    }

}
