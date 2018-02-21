<?php

namespace Modules\Platform\Listeners\MetaFields;

use Nova\Http\Request;
use Nova\Support\Facades\File;
use Nova\Support\Facades\View;

use BadMethodCallException;
use Exception;


class BaseListener
{
    /**
     * Where we store the uploaded files.
     *
     * @var string
     */
    protected $path = BASEPATH .'assets' .DS .'files';

    /**
     * @var \Nova\Http\Request
     */
    protected $request;


    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Create a View instance for the implicit (or specified) View name.
     *
     * @param  array  $data
     * @param  string|null  $view
     *
     * @return \Nova\View\View
     * @throws \BadMethodCallException
     */
    protected function createView(array $data = array(), $view = null)
    {
        if (is_null($view)) {
            list(, $caller) = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

            $view = ucfirst($caller['function']);
        }

        $path = str_replace('\\', '/', static::class);

        // Check for a valid listener on App and its Modules.
        if (preg_match('#^(App|Modules)(?:/(.+))?/Listeners/(.*)$#', $path, $matches) !== 1) {
            throw new BadMethodCallException('Invalid Listener namespace');
        }

        $view = 'Partials/' .$matches[3] .'/' .$view;

        if (($matches[1] == 'Modules') && ! empty($matches[2])) {
            $view = 'Modules/' .$matches[2] .'::' .$view;
        }

        return View::make($view, $data);
    }

    /**
     * Delete a file and log the errors if any.
     *
     * @param  string  $path
     * @return void
     */
    protected function deleteFile($path)
    {
        try {
            File::delete($path);
        }
        catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * Gets the path where are stored the (uploaded) files.
     *
     * @var string
     */
    public function getFilesPath($folder = null)
    {
        if (! is_null($folder)) {
            return $this->path .DS .$folder;
        }

        return $this->path;
    }

    /**
     * Returns the Request instance.
     *
     * @return \Nova\Http\Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
