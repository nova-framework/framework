<?php

namespace FileManager\Controllers\Admin;

use Nova\Http\Request;
use Nova\Routing\Route;
use Nova\Support\Facades\App;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Response;
use Nova\Support\Facades\View;

use Backend\Controllers\BaseController;


class Files extends BaseController
{
	/**
	 * The File Dispatcher instance.
	 *
	 * @var \Nova\Asset\DispatcherInterface
	 */
	private $fileDispatcher;


	public function __construct()
	{
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

		return $this->serveFile($path, $request);
	}

	public function thumbnails(Request $request, $thumbnail)
	{
		// Calculate the thumbnail file path.
		$path = str_replace('/', DS, BASEPATH .'storage/files/thumbnails/' .$thumbnail);

		return $this->serveFile($path, $request);
	}

	/**
	 * Return a Symfony Response instance for serving a File
	 *
	 * @param string $path
	 * @param \Nova\Http\Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	protected function serveFile($path, $request)
	{
		$dispatcher = $this->getFileDispatcher();

		return $dispatcher->serve($path, $request);
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

		return $this->fileDispatcher = App::make('asset.dispatcher');
	}
}
