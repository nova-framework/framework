<?php

namespace Modules\Files\Http\Controllers\Admin;

use Nova\Http\Request;
use Nova\Routing\Route;
use Nova\Support\Facades\App;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Response;
use Nova\Support\Facades\View;

use App\Core\BackendController;


class Files extends BackendController
{
    /**
     * The File Dispatcher instance.
     *
     * @var \Nova\Asset\DispatcherInterface
     */
    private $fileDispatcher;

    /**
     * The Request instance.
     *
     * @var \Nova\Http\Request
     */
    private $request = null;


    public function __construct(Request $request)
    {
        parent::__construct();

        $this->request = $request;

        // Setup the Middleware.
        $this->middleware('admin');
    }

    public function index()
    {
        return $this->getView()
            ->shares('title', __d('files', 'Files'));
    }

    public function connector()
    {
        // Disable the auto-rendering on a (Template) Layout.
        $this->layout = false;

        return $this->getView();
    }

    public function preview($path)
    {
        // Calculate the Preview file path.
        $path = str_replace('/', DS, BASEPATH .ltrim($path, '/'));

        return $this->serveFile($path);
    }

    public function thumbnails($thumbnail)
    {
        // Calculate the thumbnail file path.
        $path = str_replace('/', DS, BASEPATH .'storage/app/files/thumbnails/' .$thumbnail);

        return $this->serveFile($path);
    }

    /**
     * Return a Symfony Response instance for serving a File
     *
     * @param string $path
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function serveFile($path)
    {
        // Get a File Dispatcher instance.
        $dispatcher = $this->getFileDispatcher();

        return $dispatcher->serve($path, $this->request);
    }

    /**
     * Return a Files Dispatcher instance
     *
     * @return \Nova\Routing\Assets\DispatcherInterface
     */
    protected function getFileDispatcher()
    {
        if (isset($this->fileDispatcher)) return $this->fileDispatcher;

        return $this->fileDispatcher = App::make('Nova\Routing\Assets\DispatcherInterface');
    }

}
