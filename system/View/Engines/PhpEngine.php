<?php

namespace View\Engines;

use View\Engines\EngineInterface;

use Exception;


class PhpEngine implements EngineInterface
{
    /**
     * Create a new PhpEngine instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the evaluated contents of the View.
     *
     * @param  string  $path
     * @param  array   $data
     * @return string
     */
    public function get($path, array $data = array())
    {
        return $this->evaluatePath($path, $data);
    }

    /**
     * Get the evaluated contents of the View at the given path.
     *
     * @param  string  $__path
     * @param  array   $__data
     * @return string
     */
    protected function evaluatePath($__path, $__data)
    {
        ob_start();

        // Extract the rendering variables.
        foreach ($__data as $__variable => $__value) {
            ${$__variable} = $__value;
        }

        unset($__variable, $__value);

        // We'll evaluate the contents of the view inside a try/catch block so we can
        // flush out any stray output that might get out before an error occurs or
        // an exception is thrown. This prevents any partial views from leaking.
        try {
            include $__path;
        } catch (\Exception $e) {
            $this->handleViewException($e);
        }

        return ltrim(ob_get_clean());
    }

    /**
     * Handle a View Exception.
     *
     * @param  \Exception  $e
     * @return void
     *
     * @throws $e
     */
    protected function handleViewException($e)
    {
        ob_get_clean(); throw $e;
    }

}
