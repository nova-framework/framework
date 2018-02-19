<?php

namespace Modules\Platform\Listeners\MetaFields;

use Nova\Http\Request;
use Nova\Support\Facades\View;

use BadMethodCallException;


class BaseListener
{
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

        $module = ($matches[1] == 'Modules') ? $matches[2] : null;

        $view = 'Listeners/' .$matches[3] .'/' .$view;

        return View::make($view, $data, $module);
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
