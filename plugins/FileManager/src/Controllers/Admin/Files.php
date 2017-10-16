<?php

namespace AcmeCorp\FileManager\Controllers\Admin;

use Nova\Container\Container;
use Nova\Http\Request;

use AcmeCorp\Backend\Controllers\BaseController;


class Files extends BaseController
{
    /**
     * The File Dispatcher instance.
     *
     * @var \Nova\Asset\DispatcherInterface
     */
    private $fileDispatcher;

    /**
     * The IoC container instance.
     *
     * @var \Nova\Container\Container
     */
    protected $container;


    public function __construct(Container $container)
    {
        $this->container = $container;

        //
        $this->middleware('role:administrator');
    }

    public function index()
    {
        return $this->createView()
            ->shares('title', __d('files', 'Files'));
    }

    public function connector()
    {
        // Disable the auto-rendering on a (Theme) Layout.
        $this->autoLayout(false);
    }

    public function preview(Request $request, $path)
    {
        // Calculate the Preview file path.
        $path = str_replace('/', DS, BASEPATH .ltrim($path, '/'));

        return $this->getFileDispatcher()->serve($path, $request);
    }

    public function thumbnails(Request $request, $thumbnail)
    {
        // Calculate the thumbnail file path.
        $path = str_replace('/', DS, BASEPATH .'storage/files/thumbnails/' .$thumbnail);

        return $this->getFileDispatcher()->serve($path, $request);
    }

    /**
     * Return a Files Dispatcher instance
     *
     * @return \Nova\Routing\Assets\Dispatcher
     */
    protected function getFileDispatcher()
    {
        if (isset($this->fileDispatcher)) {
            return $this->fileDispatcher;
        }

        return $this->fileDispatcher = $this->container->make('assets.dispatcher');
    }
}
