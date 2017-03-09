<?php
/**
 * HandleProfilers - Implements a Response Content processing.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Http\Middleware;

use Nova\Foundation\Application;
use Nova\Forensics\Profiler;
use Nova\Forensics\Statistics;
use Nova\Http\Response;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

use Closure;


class HandleProfilers
{
    /**
     * The application implementation.
     *
     * @var \Nova\Foundation\Application
     */
    protected $app;


    /**
     * Create a new FrameGuard instance.
     *
     * @param  \Nova\Foundation\Application  $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

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

        // Get the debug flag from configuration.
        $debug = $this->app['config']->get('app.debug', false);

        if ($debug && $this->isHtmlResponse($response)) {
            return $this->processResponse($response);
        }

        return $response;
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
        $searches = array(
            '<!-- DO NOT DELETE! - Profiler -->',
            '<!-- DO NOT DELETE! - Statistics -->',
        );

        $replaces = array(
            Profiler::process(true),
            Statistics::getReport(),
        );

        $content = str_replace($searches, $replaces, $response->getContent());

        //
        $response->setContent($content);

        return $response;
    }

    protected function isHtmlResponse(SymfonyResponse $response)
    {
        if (($response instanceof RedirectResponse) || ($response instanceof JsonResponse) || ($response instanceof BinaryFileResponse) || ($response instanceof StreamedResponse)) {
            return false;
        }

        $contentType = $response->headers->get('Content-Type');

        if (! str_is('text/html*', $contentType)) return false;

        return true;
    }
}
