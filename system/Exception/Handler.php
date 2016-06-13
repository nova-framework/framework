<?php
/**
 * Handler - Implements a simple Exception Handler.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Exception;

use Exception\HttpExceptionDisplayer;
use Exception\JsonExceptionDisplayer;
use Exception\ExceptionDisplayerInterface;
use Exception\FatalErrorException as FatalError;
use Exception\HttpExceptionInterface;
use Support\Contracts\ResponsePreparerInterface;

use Closure;
use ErrorException;
use ReflectionFunction;


class Handler
{
    /**
     * The response preparer implementation.
     *
     * @var \Support\Contracts\ResponsePreparerInterface
     */
    protected $responsePreparer;

    /**
     * The exception displayer implementation.
     *
     * @var \Exception\ExceptionDisplayerInterface
     */
    protected $plainDisplayer;

    /**
     * The exception displayer implementation.
     *
     * @var \Exception\ExceptionDisplayerInterface
     */
    protected $debugDisplayer;

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
    public function __construct(
        ResponsePreparerInterface $responsePreparer,
        ExceptionDisplayerInterface $plainDisplayer,
        ExceptionDisplayerInterface $debugDisplayer,
        $debug = true)
    {
        $this->debug = $debug;

        $this->plainDisplayer = $plainDisplayer;
        $this->debugDisplayer = $debugDisplayer;

        $this->responsePreparer = $responsePreparer;
    }

    /**
     * Register the exception / error handlers.
     *
     * @return void
     */
    public function register($environment)
    {
        $this->registerErrorHandler();

        $this->registerExceptionHandler();

        if ($environment != 'testing') $this->registerShutdownHandler();
    }

    /**
     * Register the PHP error handler.
     *
     * @return void
     */
    protected function registerErrorHandler()
    {
        set_error_handler(array($this, 'handleError'));
    }

    /**
     * Register the PHP exception handler.
     *
     * @return void
     */
    protected function registerExceptionHandler()
    {
        set_exception_handler(array($this, 'handleUncaughtException'));
    }

    /**
     * Register the PHP shutdown handler.
     *
     * @return void
     */
    protected function registerShutdownHandler()
    {
        register_shutdown_function(array($this, 'handleShutdown'));
    }

    /**
     * Handle a PHP error for the application.
     *
     * @param  int     $level
     * @param  string  $message
     * @param  string  $file
     * @param  int     $line
     * @param  array   $context
     *
     * @throws \ErrorException
     */
    public function handleError($level, $message, $file = '', $line = 0, $context = array())
    {
        if (error_reporting() & $level) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
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
            return $this->prepareResponse($response);
        }

        return $this->displayException($exception);
    }

    /**
     * Handle an uncaught exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function handleUncaughtException($exception)
    {
        $this->handleException($exception)->send();
    }

    /**
     * Handle the PHP shutdown event.
     *
     * @return void
     */
    public function handleShutdown()
    {
        $error = error_get_last();

        if (! is_null($error)) {
            extract($error);

            if (! $this->isFatal($type)) return;

            $this->handleException(new FatalError($message, $type, 0, $file, $line))->send();
        }
    }

    /**
     * Determine if the error type is fatal.
     *
     * @param  int   $type
     * @return bool
     */
    protected function isFatal($type)
    {
        return in_array($type, array(E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE));
    }

    /**
     * Display the given exception to the user.
     *
     * @param  \Exception  $exception
     * @return void
     */
    protected function displayException($exception)
    {
        $displayer = $this->debug ? $this->debugDisplayer : $this->plainDisplayer;

        return $displayer->display($exception);
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
            if (! $this->handlesException($handler, $exception)) {
                continue;
            } else if ($exception instanceof HttpExceptionInterface) {
                $code = $exception->getStatusCode();
            } else {
                $code = 500;
            }

            try {
                $response = $handler($exception, $code, $fromConsole);
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

        return (! $expected->getClass() || $expected->getClass()->isInstance($exception));
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
     * Prepare the given response.
     *
     * @param  mixed  $response
     * @return \Illuminate\Http\Response
     */
    protected function prepareResponse($response)
    {
        return $this->responsePreparer->prepareResponse($response);
    }

    /**
     * Determine if we are running in the console.
     *
     * @return bool
     */
    public function runningInConsole()
    {
        return php_sapi_name() == 'cli';
    }

    /**
     * Check if is an AJAX request.
     *
     * @return bool
     */
    protected function isAjaxRequest()
    {
        return (! empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
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
