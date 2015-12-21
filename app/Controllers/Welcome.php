<?php
/**
 * Welcome controller
 *
 * @author David Carr - dave@daveismyname.com
 * @version 2.2
 * @date June 27, 2014
 * @date updated Sept 19, 2015
 */

namespace App\Controllers;

use Nova\Core\View;
use App\Core\BaseController;

/**
 * Sample controller showing a construct and 2 methods and their typical usage.
 */
class Welcome extends BaseController
{
    private $basePath;


    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function beforeFlight()
    {
        $this->basePath = str_replace(BASEPATH, '', $this->viewsPath());

        // Leave to parent's method the Flight decisions.
        return parent::beforeFlight();
    }

    protected function afterFlight($result)
    {
        // Do some processing there, even deciding to stop the Flight, if case.

        // Leave to parent's method the Flight decisions.
        return parent::afterFlight($result);
    }

    /**
     * Define Index page title and load template files
     */
    public function index()
    {
        $viewName = 'welcome';

        $filePath = $this->basePath.$viewName.'.php';

        $data['title'] = __('Welcome');
        $data['welcome_message'] = __('Hello, welcome from the welcome controller! <br/>
This content can be changed in <code>{0}</code>', $filePath);
        /*
        View::renderTemplate('header', $data);
        View::render($viewName, $data);
        View::renderTemplate('footer', $data);
        */
        View::renderView($viewName, $data, 'legacy');
    }

    /**
     * Define Subpage page title and load template files
     */
    public function subPage()
    {
        $viewName = 'subpage';

        $filePath = $this->basePath.$viewName.'.php';

        $data['title'] = __('Subpage');
        $data['welcome_message'] = __('Hello, welcome from the welcome controller and subpage method! <br/>
This content can be changed in <code>{0}</code>', $filePath);

        // Render the Page using the Content fetching and the Layout.
        $content = View::render($viewName, $data, true);

        View::renderLayout('legacy', $content, $data);
    }
}
