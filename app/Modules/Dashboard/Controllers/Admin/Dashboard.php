<?php
/**
 * Dasboard - Implements a simple Administration Dashboard.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Modules\Dashboard\Controllers\Admin;

use Core\Controller;
use Core\View;
use Helpers\Url;


class Dashboard extends Controller
{
    protected $template = 'AdminLte';
    protected $layout   = 'backend';


    public function __construct()
    {
        parent::__construct();
    }

    protected function before()
    {
        // Calculate and share on Views  the URIs.
        $uri = Url::detectUri();

        // Prepare the base URI.
        $parts = explode('/', trim($uri, '/'));

        // Make the path equal with the first part if it exists, i.e. 'admin'
        $baseUri = array_shift($parts);

        // Add to path the next part, if it exists, defaulting to 'dashboard'.
        if(! empty($parts)) {
            $baseUri .= '/' .array_shift($parts);
        } else if ($withDashboard) {
            $baseUri .= '/dashboard';
        }

        View::share('currentUri', $uri);
        View::share('baseUri',    $baseUri);

        return parent::before();
    }

    public function index()
    {
        return $this->getView()
            ->shares('title', __d('dashboard', 'Dashboard'));
    }

}
