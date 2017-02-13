<?php
/**
 * ContentGuard - Implements a Response Content processing.
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
        $config = $this->app['config'];

        $debug = $config->get('app.debug', false);

        // Get the content type.
        $contentType = $response->headers->get('Content-Type');

        if ($debug && str_is('text/html*', $contentType)) {
            return $this->insertProfilers($response);
        }

        return $response;
    }

    /**
     * Minify the Response instance Content.
     *
     * @param  \Symfony\Component\HttpFoundation\Response $response
     *
     * @return void
     */
    protected function insertProfilers(SymfonyResponse $response)
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
}
