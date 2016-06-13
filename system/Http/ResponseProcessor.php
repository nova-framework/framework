<?php
/**
 * ResponseProcessor - Implements a Response processing.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Http;

use Foundation\Application;
use Helpers\Profiler;
use Forensics\Profiler as QuickProfiler;
use Http\Response;
use Session\SessionInterface;

use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;


class ResponseProcessor
{
    /**
     * The Application instance being handled.
     *
     * @var \Foundation\Application
     */
    protected $app;

    /**
     * Class constuctor
     *
     * @return void
     */
    protected function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Finalize the Session Store and process the Response
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @return void
     */
    public static function handle(Application $app, SymfonyResponse $response)
    {
        $processor = new static($app);

        $processor->process($response);
    }

    protected function process(SymfonyResponse $response)
    {
        // Add all Request and queued Cookies.
        $this->processResponseCookies($response);

        // Finally, minify the Response's Content.
        $this->processResponseContent($response);
    }

    /**
     * Minify the Response instance Content.
     *
     * @param  \Symfony\Component\HttpFoundation\Response $response
     * @return void
     */
    protected function processResponseContent(SymfonyResponse $response)
    {
        if (! $response instanceof Response) {
            return;
        }

        $content = $response->getContent();

        if(ENVIRONMENT == 'development') {
            // Insert the QuickProfiler Widget in the Response's Content.

            $content = str_replace(
                array(
                    '<!-- DO NOT DELETE! - Forensics Profiler -->',
                    '<!-- DO NOT DELETE! - Profiler -->',
                ),
                array(
                    QuickProfiler::process(true),
                    Profiler::getReport(),
                ),
                $content
            );
        } else if(ENVIRONMENT == 'production') {
            // Minify the Response's Content.

            $search = array(
                '/\>[^\S ]+/s', // Strip whitespaces after tags, except space.
                '/[^\S ]+\</s', // Strip whitespaces before tags, except space.
                '/(\s)+/s'      // Shorten multiple whitespace sequences.
            );

            $replace = array('>', '<', '\\1');

            $content = preg_replace($search, $replace, $content);
        }

        $response->setContent($content);
    }

    /**
     * Add all the queued Cookies to Response instance and encrypt all Cookies.
     *
     * @return void
     */
    protected function processResponseCookies(SymfonyResponse $response)
    {
        $config = $this->app['config']['session'];

        $cookieJar = $this->app['cookie'];

        // Insert all queued Cookies on the Response instance.
        foreach ($cookieJar->getQueuedCookies() as $cookie) {
            $response->headers->setCookie($cookie);
        }

        if($config['encrypt'] == false) {
            // The Cookies encryption is disabled.
            return;
        }

        // Get the Encrypter instance.
        $encrypter = $this->app['encrypter'];

        // Encrypt all Cookies present on the Response instance.
        foreach ($response->headers->getCookies() as $key => $cookie)  {
            if($key == 'PHPSESSID') {
                // Leave alone the PHPSESSID.
                continue;
            }

            // Create a new Cookie with the content encrypted.
            $cookie = new SymfonyCookie(
                $cookie->getName(),
                $encrypter->encrypt($cookie->getValue()),
                $cookie->getExpiresTime(),
                $cookie->getPath(),
                $cookie->getDomain(),
                $cookie->isSecure(),
                $cookie->isHttpOnly()
            );

            $response->headers->setCookie($cookie);
        }
    }

}
