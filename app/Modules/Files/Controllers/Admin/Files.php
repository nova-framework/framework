<?php

namespace App\Modules\Files\Controllers\Admin;

use App\Core\BackendController;

use Http\Request;
use Routing\FileDispatcher;
use Routing\Route;

use Auth;
use Response;
use View;


class Files extends BackendController
{
    private $request = null;

    private $dispatcher;


    public function __construct()
    {
        parent::__construct();

        //
        $this->beforeFilter('@filterRequests');
    }

    /**
     * Filter the incoming requests.
     */
    public function filterRequests(Route $route, Request $request)
    {
        // Store the Request instance for further processing.
        $this->request = $request;

        // Check the User Authorization.
        if (Auth::user()->hasRole('administrator')) {
            // The User is authorized; continue the Execution Flow.
            return null;
        }

        if ($request->ajax()) {
            // On an AJAX Request; just return Error 403 (Access denied)
            return Response::make('', 403);
        }

        // Redirect the User to his/hers Dashboard with a warning message.
        $status = __d('files', 'You are not authorized to access this resource.');

        return Redirect::to('admin/dashboard')->withStatus($status, 'warning');
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
