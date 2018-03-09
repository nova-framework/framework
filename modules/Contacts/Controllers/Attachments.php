<?php

namespace Modules\Contacts\Controllers;

use Nova\Auth\Access\AuthorizationException;
use Nova\Container\Container;
use Nova\Database\ORM\ModelNotFoundException;
use Nova\Http\Request;
use Nova\Routing\Controller;
use Nova\Support\Facades\Gate;
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

        $path = Attachment::PATH .DS .$token .'-' .$fileName;

        try {
            $attachment = Attachment::with('message')->where('path', $path)->firstOrFail();
        }
        catch (ModelNotFoundException $e) {
            return Response::make('File Not Found', 404);
        }

        // Authorize the current User.
        if (Gate::denies('view', $attachment->message)) {
            throw new AuthorizationException();
        }

        // Get the Assets Distpatcher instance.
        $dispatcher = $this->container->make('assets.dispatcher');

        return $dispatcher->serve($path, $request, $disposition, $fileName, false);
    }
}
