<?php
/**
 * BackendController - A backend Controller for the included example Modules.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Core;

use Core\Controller;


abstract class BackendController extends Controller
{
    /**
     * The currently used Template.
     *
     * @var string
     */
    protected $template = 'AdminLte';

    /**
     * The currently used Layout.
     *
     * @var mixed
     */
    protected $layout   = 'backend';


    public function __construct()
    {
        parent::__construct();
    }

}
