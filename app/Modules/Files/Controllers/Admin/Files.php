<?php

namespace App\Modules\Files\Controllers\Admin;

use App\Core\BackendController;

use Http\Request;
use Routing\FileDispatcher;

use Auth;
use Response;
use View;


class Files extends BackendController
{
    /**
     * The Request instance.
     *
     * @var \Http\Request
     */
    private $request = null;

    /**
     * The File Dispatcher instance.
     *
     * @var \Routing\FileDispatcher
     */
    private $dispatcher;


    public function __construct()
    {
        parent::__construct();

        //
        $this->beforeFilter('@setupRequestFilter');
        $this->beforeFilter('@adminUsersFilter');
    }

    /**
     * Filter the incoming requests.
     */
    public function setupRequestFilter(Route $route, Request $request)
    {
        // Store the Request instance for further processing.
        $this->request = $request;
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
        $path = str_replace('/', DS, ROOTDIR .ltrim($path, '/'));

        return $this->serveFile($path);
    }

    public function thumbnails($thumbnail)
    {
        // Calculate the thumbnail file path.
        $path = str_replace('/', DS, APPDIR .'Storage/Files/thumbnails/' .$thumbnail);

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
        $dispatcher = $this->getDispatcher();

        return $dispatcher->serve($path, $this->request);
    }

    /**
     * Return a Files Dispatcher instance
     *
     * @return \Routing\FileDispatcher
     */
    protected function getDispatcher()
    {
        return $this->dispatcher ?: $this->dispatcher = new FileDispatcher();
    }

}
