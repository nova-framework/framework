<?php

namespace App\Modules\Attachments\Controllers;

use Nova\Container\Container;
use Nova\Http\Request;
use Nova\Http\Response;
use Nova\Routing\Controller;
use Nova\Support\Facades\Config;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;

use Carbon\Carbon;


class BaseController extends Controller
{
    /**
     * The IoC container instance.
     *
     * @var \Nova\Container\Container
     */
    protected $container;

    /**
     * The attachments storage path.
     *
     * @var string
     */
    protected $filePath;


    /**
     * Call the parent construct.
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

        //
        $this->filePath = base_path('files') .str_replace('/', DS, '/attachments/');
    }

    /**
     * Serve a File.
     *
     * @param  string $token
     * @param  string $fileName
     * @param  \Nova\Http\Request  $request
     * @param  string  $disposition
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function serveFile($token, $fileName, Request $request, $disposition = 'inline')
    {
        $path = $this->filePath .$token .'-' .$fileName;

        // Get the Assets Distpatcher instance.
        $dispatcher = $this->container->make('assets.dispatcher');

        return $dispatcher->serve($path, $request, $disposition, $fileName, false);
    }
}
