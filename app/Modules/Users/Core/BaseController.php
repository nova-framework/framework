<?php
/**
 * BaseController - A base Controller for managing the Users.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Modules\Users\Core;

use Core\Controller;
use Core\View;
use Helpers\Url;

use Session;


class BaseController extends Controller
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

        // Calculate and share on Views  the URIs.
        $uri = Url::detectUri();

        // Prepare the base URI.
        $parts = explode('/', trim($uri, '/'));

        // Make the path equal with the first part if it exists, i.e. 'admin'
        $baseUri = array_shift($parts);

        // Add to path the next part, if it exists, defaulting to 'dashboard'.
        if(! empty($parts)) {
            $baseUri .= '/' .array_shift($parts);
        } else {
            $baseUri .= '/dashboard';
        }

        View::share('currentUri', $uri);
        View::share('baseUri',    $baseUri);

        // Leave to parent's method the Execution Flow decisions.
        return parent::before();
    }

}
