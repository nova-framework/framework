<?php

namespace App\Exceptions;

use Nova\Http\Response;
use Nova\Foundation\Exceptions\Handler as ExceptionHandler;
use Nova\Session\TokenMismatchException;
use Nova\Support\Facades\View;
use Nova\Support\Facades\Redirect;

use Exception;


class Handler extends ExceptionHandler
{
	/**
	 * A list of the exception types that should not be reported.
	 *
	 * @var array
	 */
	protected $dontReport = array(
		'Symfony\Component\HttpKernel\Exception\HttpException',
		'Nova\Database\ORM\ModelNotFoundException',
		'Nova\Session\TokenMismatchException',
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
			return Redirect::guest('auth/login');
		}

		// If we got a HttpException, we will render a themed error page.
		else if ($this->isHttpException($e)) {
			$status = $e->getStatusCode();

			if (View::exists("Errors/{$status}")) {
				$data = array('exception' => $e);

				$content = View::make('Layouts/Default')
					->shares('title', "Error {$status}")
					->nest('content', "Errors/{$status}", $data)
					->render();

				return new Response($content, $status, $e->getHeaders());
			}
		}

		return parent::render($request, $e);
	}

}
