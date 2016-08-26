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

}
