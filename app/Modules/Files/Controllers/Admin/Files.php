<?php

namespace App\Modules\Files\Controllers\Admin;

use Nova\Container\Container;
use Nova\Http\Request;

use App\Modules\System\Controllers\Admin\BaseController;


class Files extends BaseController
{
    /**
     * The IoC container instance.
     *
     * @var \Nova\Container\Container
     */
    protected $container;

    /**
     * The File Dispatcher instance.
     *
     * @var \Nova\Routing\Assets\DispatcherInterface
     */
    private $fileDispatcher;


    public function __construct(Container $container)
    {
        $this->container = $container;

        //
        $this->beforeFilter('role:administrator');
    }

    public function index()
    {
        return $this->createView()
            ->shares('title', __d('files', 'Files'));
    }

    public function connector()
    {
        // Disable the auto-rendering on a (Theme) Layout.
        $this->layout = false;

        return $this->createView();
    }

    public function preview(Request $request, $path)
    {
        // Calculate the Preview file path.
        $path = str_replace('/', DS, ROOTDIR .ltrim($path, '/'));

        return $this->getFileDispatcher()->serve($path, $request);
    }

    public function thumbnails(Request $request, $thumbnail)
    {
        // Calculate the thumbnail file path.
        $path = str_replace('/', DS, STORAGE_PATH .'files/thumbnails/' .$thumbnail);

        return $this->getFileDispatcher()->serve($path, $request);
    }

    /**
     * Return a Files Dispatcher instance
     *
     * @return \Nova\Routing\Assets\DispatcherInterface
     */
    protected function getFileDispatcher()
    {
        if (isset($this->fileDispatcher)) {
            return $this->fileDispatcher;
        }

        return $this->fileDispatcher = $this->container->make('Nova\Routing\Assets\DispatcherInterface');
    }

}
