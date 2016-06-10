<?php
/**
 * Handler - A simple Exception Handler based on Whoops!
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Exception;

use Exception\PlainDisplayer;

use Whoops\Run as WhoopsRun;
use Whoops\Handler\CallbackHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Util\Misc;


class Handler
{
    /**
     * Indicates if the Application is in Debug Mode.
     *
     * @var bool
     */
    protected $debug;

    /**
     * Create a new Error Handler instance.
     *
     * @param  bool  $debug
     * @return void
     */
    public function __construct($debug = true)
    {
        $this->debug = $debug;
    }

    /**
     * Register the exception / error handlers.
     *
     * @return void
     */
    public function register()
    {
        if ($this->debug) {
            if (Misc::isAjaxRequest()) {
                $handler = new JsonResponseHandler;

                $handler->onlyForAjaxRequests(true);
            } else {
                $handler = new PrettyPageHandler();
            }
        } else {
            $handler = new PlainDisplayer();
        }

        $runner = new WhoopsRun();

        $runner->pushHandler($handler);

        $runner->register();
    }

    /**
     * Set the debug level for the handler.
     *
     * @param  bool  $debug
     * @return void
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }
}
