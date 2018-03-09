<?php

namespace Modules\Contacts\Controllers;

use Nova\Container\Container;
use Nova\Database\ORM\ModelNotFoundException;
use Nova\Http\Request;
use Nova\Routing\Controller;
use Nova\Support\Facades\Response;

use Modules\Contacts\Models\Attachment;


class Attachments extends Controller
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
    protected static $filePath = STORAGE_PATH .'files' .DS .'contacts' .DS .'attachments';


    /**
     * Call the parent construct.
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Serve a File.
     *
     * @param  \Nova\Http\Request  $request
     * @param  string $method
     * @param  string $token
     * @param  string $fileName
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function serve(Request $request, $method, $token, $fileName)
    {
        $disposition = ($method == 'download') ? 'attachment' : 'inline';

        $path = static::$filePath .DS .$token .'-' .$fileName;

        try {
            $attachment = Attachment::with('message')->where('path', $path)->firstOrFail();
        }
        catch (ModelNotFoundException $e) {
            return Response::make('File Not Found', 404);
        }

        // Get the Assets Distpatcher instance.
        $dispatcher = $this->container->make('assets.dispatcher');

        return $dispatcher->serve($path, $request, $disposition, $fileName, false);
    }
}
