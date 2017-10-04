<?php

namespace App\Foundation\Exceptions;

use Nova\Auth\AuthenticationException;
use Nova\Foundation\Exceptions\Handler as ExceptionHandler;
use Nova\Session\TokenMismatchException;
use Nova\Support\Facades\View;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Response;

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
                ->withInput($request->except('password', '_token'))
                ->with('danger', __('Validation Token has expired. Please try again!'));
        }

        // If we got a HttpException, we will render a themed error page.
        else if ($this->isHttpException($e)) {
            $status = $e->getStatusCode();

            if (View::exists("Errors/{$status}")) {
                $view = View::make('Layouts/Default')
                    ->shares('title', "Error {$status}")
                    ->nest('content', "Errors/{$status}", array('exception' => $e));

                return Response::make($view->render(), $status, $e->getHeaders());
            }
        }

        return parent::render($request, $e);
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

        return Redirect::guest('login');
    }
}
