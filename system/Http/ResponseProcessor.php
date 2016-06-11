<?php
/**
 * ResponseProcessor - Implements a Response processing.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Http;

use Core\Application;

use Helpers\Profiler;
use Forensics\Profiler as QuickProfiler;
use Http\Response as HttpResponse;
use Session\SessionInterface;

use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;
use Symfony\Component\HttpFoundation\Response as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;


class ResponseProcessor
{
    /**
     * The Application instance being handled.
     *
     * @var \Core\Application
     */
    protected $app;

    /**
     * Class constuctor
     *
     * @param  \Core\Application $app
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
        $cookieJar = $this->app['cookie'];

        $session = $this->app['session.store'];

        // Get the Session Store configuration.
        $config = $this->app['config']['session'];

        // Store the Session ID in a Cookie.
        $cookie = $cookieJar->make(
            $config['cookie'],
            $session->getId(),
            $config['lifetime'],
            $config['path'],
            $config['domain'],
            $config['secure'],
            false
        );

        $cookieJar->queue($cookie);

        // Save the Session Store data.
        $session->save();

        // Collect the garbage for the Session Store instance.
        $this->collectSessionGarbage($session, $config);

        if(is_null($response)) {
            // No further processing required.
            return;
        }

        // Add all Request and queued Cookies.
        $this->processResponseCookies($response, $config);

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
     * Remove the garbage from the session if necessary.
     *
     * @param  \Illuminate\Session\SessionInterface  $session
     * @return void
     */
    protected function collectSessionGarbage(SessionInterface $session, array $config)
    {
        $lifeTime = $config['lifetime'] * 60; // The option is in minutes.

        // Here we will see if this request hits the garbage collection lottery by hitting
        // the odds needed to perform garbage collection on any given request. If we do
        // hit it, we'll call this handler to let it delete all the expired sessions.
        if ($this->configHitsLottery($config))  {
            $session->getHandler()->gc($lifeTime);
        }
    }

    /**
     * Add all the queued Cookies to Response instance and encrypt all Cookies.
     *
     * @return void
     */
    protected function processResponseCookies(SymfonyResponse $response, array $config)
    {
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

    /**
     * Determine if the configuration odds hit the lottery.
     *
     * @param  array  $config
     * @return bool
     */
    protected function configHitsLottery(array $config)
    {
        return (mt_rand(1, $config['lottery'][1]) <= $config['lottery'][0]);
    }

}
