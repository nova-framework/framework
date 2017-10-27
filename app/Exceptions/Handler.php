<?php

namespace App\Exceptions;

use Nova\Auth\AuthenticationException;
use Nova\Foundation\Exceptions\Handler as ExceptionHandler;
use Nova\Session\TokenMismatchException;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\View;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Response;

use Symfony\Component\HttpKernel\Exception\HttpException;

use Whoops\Run as WhoopsRun;
use Whoops\Handler\PrettyPageHandler as WhoopsHandler;

use Exception;


class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = array(
        'Nova\Auth\AuthenticationException',
        'Nova\Database\ORM\ModelNotFoundException',
        'Nova\Session\TokenMismatchException',
        'Nova\Validation\ValidationException',
        'Symfony\Component\HttpKernel\Exception\HttpException',
    );

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = array(
        'password',
        'password_confirmation',
    );


    /**
     * Report or log an exception.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Nova\Http\Request  $request
     * @param  \Exception  $e
     * @return \Nova\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof TokenMismatchException) {
            return Redirect::back()
                ->withInput($request->except($this->dontFlash))
                ->with('danger', __('Validation Token has expired. Please try again!'));
        }

        return parent::render($request, $e);
    }

    /**
     * Render the given HttpException.
     *
     * @param  \Symfony\Component\HttpKernel\Exception\HttpException  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderHttpException(HttpException $e)
    {
        $status = $e->getStatusCode();

        if (View::exists("Errors/{$status}")) {
            $view = View::makeLayout('Default', 'Bootstrap')
                ->shares('title', "Error {$status}")
                ->nest('content', "Errors/{$status}", array('exception' => $e));

            return Response::make($view->render(), $status, $e->getHeaders());
        }

        return parent::renderHttpException($e);
    }

    /**
     * Convert the given exception into a Response instance.
     *
     * @param  \Exception  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertExceptionToResponse(Exception $e)
    {
        $code = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

        $headers = method_exists($e, 'getHeaders') ? $e->getHeaders() : array();

        // When the debugging is disabled, we will render a HttpException.
        $debug = Config::get('app.debug');

        if (! $debug) {
            $e = new HttpException($code, $e->getMessage(), $e, $headers, $e->getCode());

            return $this->renderHttpException($e);
        }

        $whoops = new WhoopsRun();

        $whoops->pushHandler(new WhoopsHandler());

        return Response::make($whoops->handleException($e), $code, $headers);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Nova\Http\Request  $request
     * @param  \Nova\Auth\AuthenticationException  $exception
     * @return \Nova\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->ajax() || $request->wantsJson() || $request->is('api/*')) {
            return Response::json(array('error' => 'Unauthenticated.'), 401);
        }

        $guards = $exception->guards();

        // We will use the first guard.
        $guard = array_shift($guards);

        $uri = Config::get("auth.guards.{$guard}.paths.authorize", 'login');

        return Redirect::guest($uri);
    }
}
