<?php
/**
 * HandleProfilers - Implements a Response Content processing.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Forensics\Http\Middleware;

use Nova\Http\Response;
use Nova\Support\Facades\Config;

use Forensics\Profiler;
use Forensics\Statistics;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

use Closure;


class HandleProfilers
{

	/**
	 * Handle the given request and get the response.
	 *
	 * @param  $request
	 * @param  $next
	 *
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$response = $next($request, $next);

		return $this->processResponse($response);
	}

	/**
	 * Add the profilers to the Response instance Content.
	 *
	 * @param  \Symfony\Component\HttpFoundation\Response $response
	 *
	 * @return void
	 */
	protected function processResponse(SymfonyResponse $response)
	{
		// Get the debug flag from configuration.
		$debug = Config::get('app.debug', false);

		if ($debug && $this->canPatchContent($response)) {
			$content = str_replace(
				array(
					'<!-- DO NOT DELETE! - Profiler -->',
					'<!-- DO NOT DELETE! - Statistics -->',
				),
				array(
					Profiler::process(true),
					Statistics::getReport(),
				),
				$response->getContent()
			);

			//
			$response->setContent($content);
		}

		return $response;
	}

	protected function canPatchContent(SymfonyResponse $response)
	{
		if ((! $response instanceof Response) && is_subclass_of($response, 'Symfony\Component\Http\Foundation\Response')) {
			return false;
		}

		$contentType = $response->headers->get('Content-Type');

		return str_is('text/html*', $contentType);
	}
}
