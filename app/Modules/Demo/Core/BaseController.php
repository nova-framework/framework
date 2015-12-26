<?php
/**
 * BaseController - Base Class for all App Controllers who use Templates.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 24th, 2015
 */

namespace App\Modules\Demo\Core;

use Nova\Core\View;
use Nova\Events\Manager as Events;
use Nova\Config;
use App\Core\ThemedController;

/**
 * Simple themed controller showing the typical usage of the Flight Control method.
 */
class BaseController extends ThemedController
{
    protected $layout = 'demos';


    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();

        $menuItems = Config::get('demos_menu');

        $this->set('topMenuItems', $menuItems);
        $this->set('dashboardUrl', site_url('demos'));
    }

    protected function beforeFlight()
    {
        // Leave to parent's method the Flight decisions.
        return parent::beforeFlight();
    }

    protected function afterFlight($result)
    {
        // Leave to parent's method the Flight decisions.
        return parent::afterFlight($result);
    }

}
