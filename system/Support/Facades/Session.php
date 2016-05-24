<?php
/**
 * Session - A Facade to the Session Store.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Support\Facades;

use Core\Config;
use Core\Template;

use Session\FileSessionHandler;
use Session\NativeSessionHandler;
use Session\SessionInterface;
use Session\Store as SessionStore;
use Support\Facades\Cookie;
use Support\Facades\Crypt;
use Support\Facades\Request;

use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;


class Session
{
    /**
     * The Session Store instance being handled.
     *
     * @var \Session\Store|null
     */
    protected static $sessionStore;

    /**
     * The Session Handler instance being handled.
     *
     * @var \Session\FileSessionHandler|null
     */
    protected static $sessionHandler;


    /**
     * Return a Session Store instance
     *
     * @return \Session\Store
     */
    protected static function getSessionStore()
    {
        if (isset(static::$sessionStore)) {
            return static::$sessionStore;
        }

        // Load the configuration.
        $config = Config::get('session');

        $name = $config['cookie'];

        // Get the Session ID from Cookie, fallback to null.
        $id = Cookie::get($name);

        static::$sessionStore = $session = new SessionStore($name, static::$sessionHandler, $id);

        $session->start();

        return $session;
    }

    /**
     * Intialize a Session Store instance
     *
     * @return void
     */
    public static function init()
    {
        // Load the configuration.
        $config = Config::get('session');

        $name = $config['cookie'];
        $path = $config['files'];

        $lifeTime = $config['lifetime'] * 60; // This option is in minutes.

        // Get a Session Handler instance.
        static::$sessionHandler = new FileSessionHandler($path);

        //
        ini_set('session.save_handler', 'files');

        session_set_save_handler(static::$sessionHandler, true);

        // The following prevents unexpected effects when using objects as save handlers
        register_shutdown_function('session_write_close');

        // Start the Session.
        session_set_cookie_params($lifeTime);

        session_start();

        // Store / queue a Cookie with the proper Session's information.
        Cookie::queue(session_name() ,session_id(), $config['lifetime'], null, null, false, false);
    }

    /**
     * Finalize the Session Store and send the Response
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return void
     */
    public static function finish(SymfonyResponse $response)
    {
        // Get the Session Store configuration.
        $config = Config::get('session');

        $name = $config['cookie'];

        // Get the Session Store instance.
        $session = static::getSessionStore();

        // Save the Session Store data.
        $session->save();

        // Store the Session ID in a Cookie, lasting five years.
        Cookie::queue($name, $session->getId(), Cookie::FIVEYEARS, null, null, false, false);

        // Collect the garbage for the Session Store instance.
        static::collectGarbage($session, $config);

        // Finally, add all Request and queued Cookies on Response instance.
        static::processCookies($response);

        // Prepare the Response.
        $request = Request::instance();

        $response->prepare($request);

        // Send the Response.
        $response->send();
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
    protected static function processCookies(SymfonyResponse $response)
    {
        foreach (Cookie::getQueuedCookies() as $cookie) {
            $response->headers->setCookie($cookie);
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

    /**
     * Return a Session Store instance
     *
     * @return \Session\Store
     */
    public static function instance()
    {
        return static::getSessionStore();
    }

    /**
     * Display a one time Message, then clear it from the Session.
     *
     * @param  string $name default Session name
     *
     * @return string
     */
    public static function message($name = 'success')
    {
        $instance = static::getSessionStore();

        if (! $instance->has($name)) {
            return null;
        }

        // Pull the Message from the Session Store.
        $message = $instance->remove($name);

        if (is_array($message)) {
            // The Message is structured in the New Style.
            $name    = $message['type'];
            $message = $message['text'];
        }

        // Prepare the allert Type and Icon.
        $type = null;

        switch ($name) {
            case 'info':
                $icon = 'info';
                break;
            case 'warning':
                $icon = 'warning';
                break;
            case 'danger':
                $icon = 'bomb';
                break;
            default:
                $icon = 'check';
                $type = 'success';
        }

        $type = ($type !== null) ? $type : $name;

        // Fetch the associated Template Fragment and return the result.
        return Template::make('message', compact('type', 'icon', 'message'))->fetch();
    }

    /**
     * Magic Method for calling the methods on the default Session Store instance.
     *
     * @param $method
     * @param $params
     *
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        // Get the Session Store instance.
        $instance = static::getSessionStore();

        // Call the non-static method from the Request instance.
        return call_user_func_array(array($instance, $method), $params);
    }
}
