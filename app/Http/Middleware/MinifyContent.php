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


class MinifyContent
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

        //
        $contentType = $response->headers->get('Content-Type');

        if (! str_is('text/html*', $contentType)) {
            return $response;
        }

        return $this->processResponseContent($response);
    }

    /**
     * Minify the Response instance Content.
     *
     * @param  \Symfony\Component\HttpFoundation\Response $response
     * @param bool $debug
     *
     * @return void
     */
    protected function processResponseContent(SymfonyResponse $response)
    {
        $config = $this->app['config'];

        //
        $debug = $config->get('app.debug', false);

        if(! $debug) {
            // Minify the Response's Content and return it.
            return $this->minifyResponseContent($response);
        }

        // Insert the QuickProfiler Widget in the Response's Content.
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

    protected function minifyResponseContent(SymfonyResponse $response)
    {
        $searches = array(
            '/\>[^\S ]+/s', // Strip whitespaces after tags, except space.
            '/[^\S ]+\</s', // Strip whitespaces before tags, except space.
            '/(\s)+/s'      // Shorten multiple whitespace sequences.
        );

        $replaces = array('>', '<', '\\1');

        $content = preg_replace($searches, $replaces, $response->getContent());

        //
        $response->setContent($content);

        return $response;
    }
}
