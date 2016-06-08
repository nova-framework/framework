<?php
/**
 * Welcome controller
 *
 * @author David Carr - dave@novaframework.com
 * @version 3.0
 */

namespace App\Controllers;

use Core\View;
use Core\Controller;
use Routing\Router;

use Language;
use Session;

/**
 * Sample controller showing a construct and 2 methods and their typical usage.
 */
class Welcome extends Controller
{
    protected $langCode;

    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();

        // Setup the Controller's Language code.
        $this->langCode = Language::code();

        // Load the Language file.
        $this->language->load('Welcome');
    }

    protected function before()
    {
        // Process the Multi-Language.
        $language = Router::getLanguage();

        if($language != $this->langCode) {
            $this->langCode = $language;

            $this->language->load('Welcome', $this->langCode);
        }

        // Leave to parent's method the Execution Flow decisions.
        return parent::before();
    }

    /**
     * Define Index page title and load template files.
     */
    public function index()
    {
        $data['title'] = $this->language->get('welcomeText', $this->langCode);
        $data['welcomeMessage'] = $this->language->get('welcomeMessage', $this->langCode);

        View::renderTemplate('header', $data);
        View::render('Welcome/Welcome', $data);
        View::renderTemplate('footer', $data);
    }

    /**
     * The New Style Rendering - create and return a proper View instance.
     */
    public function subPage()
    {
        return View::make('Welcome/SubPage')
            ->shares('title', $this->trans('subpageText', $this->langCode))
            ->withWelcomeMessage($this->trans('subpageMessage', $this->langCode));
    }
}
