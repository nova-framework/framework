<?php
/**
 * Controller - base controller
 *
 * @author David Carr - dave@novaframework.com
 * @version 3.0
 */

namespace Core;

use Core\Language;
use Core\BaseView;
use Core\Template;
use Core\View;
use Helpers\Hooks;

/**
 * Core controller, all other controllers extend this base controller.
 */
abstract class Controller
{
    private $method = null;
    private $params = array();

    /**
     * The current used Template.
     *
     * @var string
     */
    protected $template = null;

    /**
     * The current used Layout.
     *
     * @var string
     */
    protected $layout = 'default';

    /**
     * Language variable to use the languages class.
     *
     * @var string
     */
    public $language;

    /**
     * On run make an instance of the config class and view class.
     */
    public function __construct()
    {
        // Adjust to default Template if no one is defined.
        $this->template = ($this->template !== null) ?: TEMPLATE;

        /** Initialise the Language object */
        $this->language = new Language();
    }

    /**
     * Execute Controller Method
     * @return bool
     */
    public function execute($method, $params = array())
    {
        // Initialize the Controller's variables.
        $this->method = $method;
        $this->params = $params;

        // Before Action execution stage.
        if ($this->before() === false) {
            // Is wanted to stop the execution.
            return false;
        }

        // Execute the requested Method with the given arguments.
        $result = call_user_func_array(array($this, $method), $params);

        // After Action execution stage.
        if (($result !== null) && ! is_bool($result)) {
            return $this->after($result);
        }

        return true;
    }

    /**
     * Method automatically invoked before the current Action, stopping the flight
     * when it return false. This Method is supposed to be overriden for using it.
     */
    protected function before()
    {
        // Run the Hooks associated to Views.
        $hooks = Hooks::get();

        foreach (array('afterBody', 'css', 'js') as $hook) {
            $result = $hooks->run($hook);

            // Share the result into Views.
            View::share($hook, $result);
        }

        // Return true to continue the processing.
        return true;
    }

    /**
     * Method automatically invoked after the current Action, when it not return a
     * null or boolean value. This Method is supposed to be overriden for using it.
     *
     * Note that the Action's returned value is passed to this Method as parameter.
     */
    protected function after($result)
    {
        if (is_string($result)) {
            // The data is a String; send the Response Headers and output it.
            Response::sendHeaders();

            echo $result;
        } else if (is_array($result)) {
            // The data is an Array; prepare and send a JSON response.
            header('Content-Type: application/json', true);

            echo json_encode($result);
        } else if (! $result instanceof BaseView) {
            // The data is not a View instance; no further processing required.
            return true;
        }

        //
        // Execute the default Template-based rendering of the given View instance.

        if ((! $result instanceof Template) && ($this->layout !== false)) {
            // The View instance is NOT a Template, but we have a Layout specified.
            $data = $result->localData();

            $result = Template::make($this->layout, $data, $this->template)
                ->with('content', $result->fetch());
        }

        // Send the HTTP headers and render the given View or Template.
        $result->display();

        return true;
    }

    /**
     * Return a translated string.
     * @return string
     */
    protected function trans($str, $code = LANGUAGE_CODE)
    {
        return $this->language->get($str, $code);
    }

    /**
     * @return mixed
     */
    protected function template()
    {
        return $this->template;
    }

    /**
     * @return mixed
     */
    protected function layout()
    {
        return $this->layout;
    }

    /**
     * @return mixed
     */
    protected function method()
    {
        return $this->method;
    }

    /**
     * @return array
     */
    protected function params()
    {
        return $this->params;
    }
}
