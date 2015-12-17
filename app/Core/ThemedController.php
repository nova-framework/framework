<?php
/**
 * ThemedController - Base Class for all themed Controllers.
 *
 * @author Virgil-Adrian Teaca - virgil@@giulianaeassociati.com
 * @version 3.0
 * @date December 17th, 2015
 */

namespace App\Core;

use Smvc\Core\View;
use Smvc\Core\Controller;

/**
 * Sample controller showing a construct and 2 methods and their typical usage.
 */
class ThemedController extends Controller
{
    protected $layout = 'themed';


    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function afterFlight($result)
    {
        $this->renderLayout($result);

        // Return false to stop the Flight.
        return false;
    }

    protected function renderLayout($data)
    {
        $title = '';

        if($data instanceof View) {
            $title = $data->get('title');

            $content = $data->fetch();
        }
        else if(is_array($data)) {
            $content = json_encode($data);
        }
        else if(is_integer($data)) {
            // Just to see '0' on webpage and nothing more.
            $content = sprintf('%d', $data);
        }
        else if(is_bool($data)) {
            // Just to see '0' on webpage and nothing more.
            $content = $data ? 'true' : 'false';
        }
        else {
            $content = $data;
        }

        $title = ! empty($title) ? $title : __('Welcome');

        View::layout($this->layout())
            ->withTitle($title)
            ->withContent($content)
            ->display();
    }

}
