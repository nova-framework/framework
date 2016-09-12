<?php

namespace Routing\Assets;

use Config\Config;
use Http\Response;
use Routing\Assets\DispatcherInterface;
use Support\Str;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;

use Carbon\Carbon;

use LogicException;


class DefaultDispatcher implements DispatcherInterface
{
    /**
     * The valid Vendor paths.
     * @var array
     */
    protected $paths = array();

    /**
     * The currently accepted encodings for Response content compression.
     *
     * @var array
     */
    protected static $algorithms = array('gzip', 'deflate');


    /**
     * Create a new Default Dispatcher instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->paths = Config::get('routing.assets.paths', array());
    }

    /**
     * Dispatch a Assets File Response.
     *
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function dispatch(SymfonyRequest $request)
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

        // Check the URI on the Request and prepare the Asset File path.
        $uri = $request->path();

        $path = null;
        
        if (preg_match('#^(templates|modules)/([^/]+)/assets/([^/]+)/(.*)$#i', $uri, $matches)) {
            $module = Str::studly($matches[2]);

            if (strtolower($matches[1]) == 'modules') {
                // The Asset File is located on a Module.
                $path = static::getModulePath($module, $matches[3], $matches[4]);
            } else {
                // The Asset File is located on a Template.
                $path = static::getTemplatePath($module, $matches[3], $matches[4]);
            }
        } else if (preg_match('#^(assets|vendor)/(.*)$#i', $uri, $matches)) {
            $path = $matches[2];

            if (strtolower($matches[1]) == 'assets') {
                // The Asset File is located on root 'assets' folder.
                $path = ROOTDIR .'assets' .DS .$path;
            } else if (Str::startsWith($path, $this->paths)) {
                // The Asset File is located on one of the valid Vendor paths.
                $path = ROOTDIR .'vendor' .DS .str_replace('/', DS, $path);
            } else {
                $path = null;
            }
        }

        // If the path is null, then the current URI is not for a Asset File.
        if (is_null($path)) return null;

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
    public function serve($path, SymfonyRequest $request)
    {
        if (! file_exists($path)) {
            return new Response('File Not Found', 404);
        } else if (! is_readable($path)) {
            return new Response('Unauthorized Access', 403);
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

        if (($contentType == 'application/javascript') || str_is('text/*', $contentType)) {
            $response = $this->createFileResponse($path, $request);
        } else {
            $response = $this->createBinaryFileResponse($path);
        }

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

    protected function createFileResponse($path, SymfonyRequest $request)
    {
        // Get the accepted encodings from Request instance.
        $acceptEncoding = $request->headers->get('Accept-Encoding');

        if (! is_null($acceptEncoding)) {
            $acceptEncoding = array_map('trim', explode(',', $acceptEncoding));
        } else {
            $acceptEncoding = array();
        }

        // Create a Response instance.
        $response = new Response(file_get_contents($path), 200);

        // Setup the Last-Modified header.
        $lastModified = Carbon::createFromTimestampUTC(filemtime($path));

        $response->headers->set('Last-Modified', $lastModified->format('D, j M Y H:i:s') .' GMT');

        return $this->compressResponseContent($response, $acceptEncoding);
    }

    protected function createBinaryFileResponse($path, $contentDisposition = null)
    {
        $contentDisposition = $contentDisposition ?: 'inline';

        return new BinaryFileResponse($path, 200, array(), true, $contentDisposition, true, false);
    }

    protected function compressResponseContent(SymfonyResponse $response, array $acceptEncoding)
    {
        // Calculate the available algorithms.
        $algorithms = array_values(array_intersect($acceptEncoding, static::$algorithms));

        // If there are no available compression algorithms, just return the Response instance.
        if (empty($algorithms)) {
            $response->headers->set('Content-Length', strlen($response->getContent()));

            return $response;
        }

        // Get the (first) compression algorithm.
        $algorithm = array_shift($algorithms);

        // Compress the Response content.
        if ($algorithm == 'gzip') {
            $content = gzencode($response->getContent(), -1, FORCE_GZIP);
        } else if ($algorithm == 'deflate') {
            $content = gzencode($response->getContent(), -1, FORCE_DEFLATE);
        } else {
            throw new LogicException('Unknow encoding algorithm: ' .$algorithm);
        }

        // Setup the (new) Response content.
        $response->setContent($content);

        // Setup the (new) Content Length.
        $response->headers->set('Content-Length', strlen($content));

        // Setup the Content Encoding.
        $response->headers->set('Content-Encoding', $algorithm);

        return $response;
    }

    /**
     * Get the path of a Asset file
     * @return string|null
     */
    protected static function getModulePath($module, $folder, $path)
    {
        $path = str_replace('/', DS, $path);

        //
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
