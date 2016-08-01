<?php
/**
 * Controller - base controller
 *
 * @author David Carr - dave@novaframework.com
 * @version 3.0
 */

namespace Routing;

use Core\Config;
use Http\Response;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Event;


/**
 * Core controller, all other controllers extend this base controller.
 */
abstract class Controller
{
    /**
     * The requested Method by Router.
     *
     * @var string|null
     */
    private $method = null;

    /**
     * The parameters given by Router.
     *
     * @var array
     */
    private $params = array();

    
    /**
     * Create a new Controller instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the Controller Method
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throw \Exception
     */
    public function execute($method, $params = array())
    {
        // Initialise the Controller's variables.
        $this->method = $method;
        $this->params = $params;

        // Notify the interested Listeners about the iminent Controller's execution.
        Event::fire('framework.controller.executing', array($this, $method, $params));

        // Before the Action execution stage.
        $response = $this->before();

        // When the Before Stage do not return a Symfony Response, execute the requested
        // Method with the given arguments, capturing its returned value on our response.
        if (! $response instanceof SymfonyResponse) {
            $response = call_user_func_array(array($this, $method), $params);
        }

        // After the Action execution stage.
        $this->after($response);

        // Do the final post-processing stage and return the response.
        return $this->processResponse($response);
    }

    /**
     * Create from the given result a Response instance and send it.
     *
     * @param mixed  $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function processResponse($response)
    {
        if (! $response instanceof SymfonyResponse) {
            $response = new Response($response);
        }

        return $response;
    }

    /**
     * Method automatically invoked before the current Action, stopping the flight
     * when it returns false. This Method is supposed to be overriden for using it.
     */
    protected function before()
    {
        // Return true to continue the processing.
        return true;
    }

    /**
     * This method automatically invokes after the current Action and is supposed
     * to be overriden for using it.
     *
     * Note that the Action's returned value is passed to this Method as parameter.
     */
    protected function after($result)
    {
        //
    }

    /**
     * @return mixed
     */
    protected function getMethod()
    {
        return $this->method;
    }

    /**
     * @return array
     */
    protected function getParams()
    {
        return $this->params;
    }

    /**
     * Handle calls to missing methods on the controller.
     *
     * @param  array   $parameters
     * @return mixed
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function missingMethod($parameters = array())
    {
        throw new NotFoundHttpException("Controller method not found.");
    }

    /**
     * Handle calls to missing methods on the controller.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        throw new \BadMethodCallException("Method [$method] does not exist.");
    }

}
