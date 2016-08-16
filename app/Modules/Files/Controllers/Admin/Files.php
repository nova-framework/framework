<?php

namespace App\Modules\Files\Controllers\Admin;

use App\Core\Controller;

use Routing\FileDispatcher;

use Auth;
use Request;


class Files extends Controller
{
    private $dispatcher;


    /**
     * Create a new Files Controller instance.
     */
    public function __construct()
    {
        parent::__construct();

        //
        $this->dispatcher = new FileDispatcher();
    }

    protected function before()
    {
        // Check the User Authorization.
        if (! Auth::user()->hasRole('administrator')) {
            $status = __d('files', 'You are not authorized to access this resource.');

            return Redirect::to('admin/dashboard')->withStatus($status, 'warning');
        }

        // Leave to parent's method the Execution Flow decisions.
        return parent::before();
    }

    public function index()
    {
        return $this->getView()
            ->shares('title', __d('files', 'Files'));
    }

    public function connector()
    {
        // Disable the auto-rendering of the returned View instance on a Layout.
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
     * Serve a File.
     *
     * @param string $path
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function serveFile($path)
    {
        $request = Request::instance();

        // Get the Response from File Dispatcher instance and return it.
        return $this->dispatcher->serve($path, $request);
    }

}
