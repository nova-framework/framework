<?php
/**
 * Controller - A base Controller for the included examples.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Core;

use Core\Controller as BaseController;

use Request;
use Session;
use View;


class Controller extends BaseController
{
    protected $template = 'AdminLte';
    protected $layout   = 'backend';


    public function __construct()
    {
        parent::__construct();
    }

    protected function before()
    {
        // Share on Views the CSRF Token.
        View::share('csrfToken', Session::token());

        // Calculate the URIs and share them on Views.
        $uri = Request::path();

        // Prepare the base URI.
        $baseUri = trim($uri, '/');

        if (! empty($baseUri)) {
            $parts = explode('/', $baseUri);

            // Make the path equal with the first part if it exists, i.e. 'admin'
            $baseUri = array_shift($parts);

            // Add to path the next part, if it exists, defaulting to 'dashboard'.
            if (! empty($parts)) {
                $baseUri .= '/' .array_shift($parts);
            } else if ($baseUri == 'admin') {
                $baseUri .= '/dashboard';
            }
        } else {
            // Respect the URI conventions.
            $baseUri = '/';
        }

        View::share('currentUri', $uri);
        View::share('baseUri', $baseUri);

        // Leave to parent's method the Execution Flow decisions.
        return parent::before();
    }

}
