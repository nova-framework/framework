<?php
/**
 * Controller - base controller
 *
 * @author David Carr - dave@novaframework.com
 * @version 3.0
 */

namespace Core;

use Core\BaseView;
use Core\Language;
use Core\Template;
use Core\View;
use Helpers\Hooks;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

use Response;
use Session;


/**
 * Core controller, all other controllers extend this base controller.
 */
abstract class Controller
{
    private $method = null;
    private $params = array();

    /**
     * The currently used Template.
     *
     * @var string
     */
    protected $template = null;

    /**
     * The currently used Layout.
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
     * On the initial run, create an instance of the config class and the view class.
     */
    public function __construct()
    {
        // Adjust to the default Template, if it is not defined.
        $this->template = $this->template ?: TEMPLATE;

        // Initialise the Language object.
        $this->language = Language::getInstance();
    }

    /**
     * Execute the Controller Method
     * @return bool
     */
    public function execute($method, $params = array())
    {
        // Initialise the Controller's variables.
        $this->method = $method;
        $this->params = $params;

        // Before the Action execution stage.
        if ($this->before() === false) {
            // This is needed to stop the execution.
            return false;
        }

        // Execute the requested Method with the given arguments.
        $result = call_user_func_array(array($this, $method), $params);

        // The Method returned a Response instance; send it and stop the processing.
        if ($result instanceof SymfonyResponse) {
            // Finish the Session and send the Response.
            Session::finish($result);

            return true;
        }

        // After the Action execution stage.
        $retval = $this->after($result);

        if($retval !== false) {
            // Create the Response and send it.
            return $this->createResponse($result);
        }

        return true;
    }

    /**
     * Create from the given result a Response instance and send it.
     *
     * @param mixed  $result
     * @return bool
     */
    protected function createResponse($result)
    {
        if ($result === null) {
            // Retrieve the legacy View instances.
            $items = View::getLegacyItems();

            if(empty($items)) {
                // There are no legacy View instances; quit processing.
                return true;
            }

            // Prepare the Response's Content.
            $content = '';

            foreach ($items as $item) {
                // Fetch the current View instance to content.
                $content .= $item->fetch();
            }

            // Retrieve also the legacy Headers.
            $headers = View::getLegacyHeaders();

            // Create a Response instance.
            $response = Response::make($content, 200, $headers);

            // Finish the Session and send the Response.
            Session::finish($response);

            return true;
        } else if (! $result instanceof BaseView) {
            // The result is not a BaseView instance; quit the processing.
            return true;
        }

        if ((! $result instanceof Template) && ($this->layout !== false)) {
            // A View instance, having a Layout specified; create a Template instance.
            $result = Template::make($this->layout, $this->template)
                ->with('content', $result->fetch());
        }

        // Create a Response instance.
        $response = Response::make($result);

        // Finish the Session and send the Response.
        Session::finish($response);

        return true;
    }

    /**
     * Method automatically invoked before the current Action, stopping the flight
     * when it returns false. This Method is supposed to be overriden for using it.
     */
    protected function before()
    {
        // Run the Hooks associated to the Views.
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
     * This method automatically invokes after the current Action, when it does not return a
     * null or boolean value. This Method is supposed to be overriden for using it.
     *
     * Note that the Action's returned value is passed to this Method as parameter.
     */
    protected function after($result)
    {
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
