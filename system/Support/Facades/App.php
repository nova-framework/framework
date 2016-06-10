<?php
/**
 * App - A Facade to the Application.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Support\Facades;

use Helpers\Profiler;
use Forensics\Profiler as QuickProfiler;
use Http\Response as HttpResponse;
use Session\SessionInterface;

use Support\Facades\Cookie;
use Support\Facades\Config;
use Support\Facades\Crypt;
use Support\Facades\Request;
use Support\Facades\Session;

use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;



class App
{
    /**
     * Initialize the Application instance
     *
     * @return void
     */
    public static function init()
    {
    }

    /**
     * Finalize the Session Store and send the Response
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @return void
     */
    public static function finish(SymfonyResponse $response)
    {
        // Get the Session Store configuration.
        $config = Config::get('session');

        // Get the Request instance.
        $request = Request::instance();

        // Get the Session Store instance.
        $session = Session::instance();

        // Store the Session ID in a Cookie.
        $cookie = Cookie::make(
            $config['cookie'],
            $session->getId(),
            $config['lifetime'],
            $config['path'],
            $config['domain'],
            $config['secure'],
            false
        );

        Cookie::queue($cookie);

        // Save the Session Store data.
        $session->save();

        // Collect the garbage for the Session Store instance.
        static::collectGarbage($session, $config);

        if(is_null($response)) {
            // No further processing required.
            return;
        }

        // Add all Request and queued Cookies.
        static::processCookies($response, $config);

        // Finally, minify the Response's Content.
        static::processContent($response);

        // Prepare the Response instance for sending.
        $response->prepare($request);

        // Send the Response.
        $response->send();
    }

    /**
     * Minify the Response instance Content.
     *
     * @param  \Symfony\Component\HttpFoundation\Response $response
     * @return void
     */
    protected static function processContent(SymfonyResponse $response)
    {
        if (! $response instanceof HttpResponse) {
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
    protected static function collectGarbage(SessionInterface $session, array $config)
    {
        $lifeTime = $config['lifetime'] * 60; // The option is in minutes.

        // Here we will see if this request hits the garbage collection lottery by hitting
        // the odds needed to perform garbage collection on any given request. If we do
        // hit it, we'll call this handler to let it delete all the expired sessions.
        if (static::configHitsLottery($config))  {
            $session->getHandler()->gc($lifeTime);
        }
    }

    /**
     * Add all the queued Cookies to Response instance and encrypt all Cookies.
     *
     * @return void
     */
    protected static function processCookies(SymfonyResponse $response, array $config)
    {
        // Insert all queued Cookies on the Response instance.
        foreach (Cookie::getQueuedCookies() as $cookie) {
            $response->headers->setCookie($cookie);
        }

        if($config['encrypt'] == false) {
            // The Cookies encryption is disabled.
            return;
        }

        // Encrypt all Cookies present on the Response instance.
        foreach ($response->headers->getCookies() as $key => $cookie)  {
            if($key == 'PHPSESSID') {
                // Leave alone the PHPSESSID.
                continue;
            }

            // Create a new Cookie with the content encrypted.
            $cookie = new SymfonyCookie(
                $cookie->getName(),
                Crypt::encrypt($cookie->getValue()),
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
    protected static function configHitsLottery(array $config)
    {
        return (mt_rand(1, $config['lottery'][1]) <= $config['lottery'][0]);
    }
}
