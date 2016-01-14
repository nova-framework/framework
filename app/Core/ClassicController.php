<?php
/**
 * ClassicController - Base Class for all Classic Controllers.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 18th, 2015
 */

namespace App\Core;

use Nova\Core\View;
use App\Core\BaseController;

/**
 * Simple themed controller showing the typical usage of the Flight Control method.
 */
class ClassicController extends BaseController
{
    protected $layout = 'default';

    protected $events = null;


    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Before Flight
     * @return bool
     */
    protected function beforeFlight()
    {
        // Leave to parent's method the Flight decisions.
        return parent::beforeFlight();
    }

    /**
     * After Flight
     * @param mixed $result
     * @return bool
     */
    protected function afterFlight($result)
    {
        if (($result === false) || ! $this->autoRender) {
            // Stop the Flight.
            return false;
        }

        if (($result === true) || is_null($result)) {
            $data = $this->data();

            if ($this->useLayout) {
                $content = View::renderView($this->method(), $data, true);

                View::renderLayout($this->layout(), $content, $data);

                // Stop the Flight.
                return false;
            }

            View::renderView($this->method(), $data, false);

            // Stop the Flight.
            return false;
        }

        // Leave to parent's method the Flight decisions.
        return parent::afterFlight($result);
    }
}
