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

use Response;

use Closure;
use ReflectionFunction;


class Handler
{
    /**
     * Indicates if the Application is in Debug Mode.
     *
     * @var bool
     */
    protected $debug;

    /**
     * All of the register exception handlers.
     *
     * @var array
     */
    protected $handlers = array();

    /**
     * All of the handled error messages.
     *
     * @var array
     */
    protected $handled = array();

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

        //
        $runner = new WhoopsRun();

        $runner->pushHandler($handler);

        $runner->register();
    }

    /**
     * Handle an exception for the application.
     *
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleException($exception)
    {
        $response = $this->callCustomHandlers($exception);

        if (! is_null($response)) {
            return $response;
        }

        return $this->displayException($exception);
    }

    /**
     * Display the given exception to the user.
     *
     * @param  \Exception  $exception
     * @return void
     */
    protected function displayException($exception)
    {
        return Response::make($exception->getMessage());
    }

    /**
     * Handle the given exception.
     *
     * @param  \Exception  $exception
     * @param  bool  $fromConsole
     * @return void
     */
    protected function callCustomHandlers($exception, $fromConsole = false)
    {
        foreach ($this->handlers as $handler) {
            if ( ! $this->handlesException($handler, $exception)) {
                continue;
            }

            try {
                $response = $handler($exception, 500, $fromConsole);
            } catch (\Exception $e) {
                $response = $this->formatException($e);
            }

            if (isset($response) && ! is_null($response)) {
                return $response;
            }
        }
    }

    /**
     * Determine if the given handler handles this exception.
     *
     * @param  Closure    $handler
     * @param  \Exception  $exception
     * @return bool
     */
    protected function handlesException(Closure $handler, $exception)
    {
        $reflection = new ReflectionFunction($handler);

        return (($reflection->getNumberOfParameters() == 0) || $this->hints($reflection, $exception));
    }

    /**
     * Determine if the given handler type hints the exception.
     *
     * @param  ReflectionFunction  $reflection
     * @param  \Exception  $exception
     * @return bool
     */
    protected function hints(ReflectionFunction $reflection, $exception)
    {
        $parameters = $reflection->getParameters();

        $expected = $parameters[0];

        return ! $expected->getClass() || $expected->getClass()->isInstance($exception);
    }

    /**
     * Format an exception thrown by a handler.
     *
     * @param  \Exception  $e
     * @return string
     */
    protected function formatException(\Exception $e)
    {
        if ($this->debug) {
            $location = $e->getMessage().' in '.$e->getFile().':'.$e->getLine();

            return 'Error in exception handler: '.$location;
        }

        return 'Error in exception handler.';
    }

    /**
     * Register an application error handler.
     *
     * @param  Closure  $callback
     * @return void
     */
    public function error(Closure $callback)
    {
        array_unshift($this->handlers, $callback);
    }

    /**
     * Register an application error handler at the bottom of the stack.
     *
     * @param  Closure  $callback
     * @return void
     */
    public function pushError(Closure $callback)
    {
        $this->handlers[] = $callback;
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
