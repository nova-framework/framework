<?php

namespace App\Modules\MediaManager\Controllers\Admin;

use App\Core\Controller;

use Http\Response;
use Routing\FileDispatcher;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;

use Auth;
use Input;
use Request;


class Files extends Controller
{
    private $dispatcher;

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
            $status = __d('users', 'You are not authorized to access this resource.');

            return Redirect::to('admin/dashboard')->withStatus($status, 'warning');
        }

        // Leave to parent's method the Execution Flow decisions.
        return parent::before();
    }

    public function index()
    {
        return $this->getView()
            ->shares('title', __d('media_manager', 'File Manager'));
    }

    public function connector()
    {
        return $this->getView();
    }

    public function preview()
    {
        $file = Input::get('file');

        $path = filter_var($file, FILTER_SANITIZE_URL);

        // Calculate the preview file path.
        $path = str_replace('/', DS, ltrim($path, '/'));

        return $this->serve(ROOTDIR .$path);
    }

    public function thumbnails($thumbnail)
    {
        $thumbnail = filter_var($thumbnail, FILTER_SANITIZE_URL);

        // Calculate the thumbnail file path.
        $path = str_replace('/', DS, 'Storage/Files/thumbnails/' .$thumbnail);

        return $this->serve(APPDIR .$path);
    }

    /**
     * Serve a File.
     *
     * @param string $path
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function serve($path)
    {
        // Get the Response required to serve this file path.
        $response = $this->dispatcher->serve($path);

        // Setup the Response against the Request instance.
        if($response instanceof BinaryFileResponse) {
            $request = Request::instance();

            $response->isNotModified($request);
        }

        return $response;
    }

}
