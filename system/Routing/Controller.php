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

        // Before the Action execution stage.
        $response = $this->before();

        // When the Before Stage return a null Response, execute the requested Action.
        if (is_null($response)) {
            $response = call_user_func_array(array($this, $method), $params);
        }

        // After the Action execution stage.
        $this->after($response);

        // Do the final post-processing stage and return the response.
        return $this->processResponse($response);
    }

    /**
     * Process the response and return it.
     *
     * @param mixed  $response
     *
     * @return mixed
     */
    protected function processResponse($response)
    {
        return $response;
    }

    /**
     * This method automatically invokes before the current Action and is supposed
     * to be overriden for using it.
     */
    protected function before()
    {
        //
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
