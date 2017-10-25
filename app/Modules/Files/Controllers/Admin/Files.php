<?php

namespace App\Modules\Files\Controllers\Admin;

use Nova\Container\Container;
use Nova\Http\Request;
use Nova\Support\Facades\Config;

use App\Modules\Platform\Controllers\Admin\BaseController;
use App\Modules\Files\Support\Connector;

use elFinder;


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
        $this->middleware('role:administrator');
    }

    public function index()
    {
        return $this->createView()
            ->shares('title', __d('files', 'Files'));
    }

    public function connector()
    {
        // Retrieve the elFinder options.
        $options = Config::get('files::elFinder');

        // Create a elFinder instance.
        $elFinder = new elFinder($options);

        // Create a Connector instance.
        $connector = new Connector($elFinder, true);

        $connector->run();

        return $connector->getResponse();
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

        return $this->fileDispatcher = $this->container->make('assets.dispatcher');
    }

}
