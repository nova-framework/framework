<?php
/**
 * RequestProcessor - Implements a Request processing.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Http;

use Foundation\Application;
use Encryption\DecryptException;
use Http\Request;


class RequestProcessor
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
     * @param  \Foundation\Application $app
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
    public static function handle(Application $app, Request $request)
    {
        $instance = new static($app);

        $instance->process($request);
    }

    protected function process(Request $request)
    {
        // Retrieve the Session configuration.
        $config = $this->app['config']['session'];

        if($config['encrypt'] == false) {
            // The Cookies encryption is disabled.
            return;
        }

        // Get the Encrypter instance.
        $encrypter = $this->app['encrypter'];

        foreach ($request->cookies as $name => $cookie) {
            if($name == 'PHPSESSID') {
                // Leave alone the PHPSESSID.
                continue;
            }

            try {
                if(is_array($cookie)) {
                    $decrypted = array();

                    foreach ($cookie as $key => $value) {
                        $decrypted[$key] = $encrypter->decrypt($value);
                    }
                } else {
                    $decrypted = $encrypter->decrypt($cookie);
                }

                $request->cookies->set($name, $decrypted);
            } catch (DecryptException $e) {
                $request->cookies->set($name, null);
            }
        }
    }

}
