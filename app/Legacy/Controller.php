<?php
/**
 * LegacyController - A base controller with legacy API support.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Legacy;

use App\Core\Controller as BaseController;
use Http\Request;
use Http\Response;
use Routing\Route;

use Language;
use View;

use BadMethodCallException;


abstract class Controller extends BaseController
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
     * Language variable to use the languages class.
     *
     * @var string
     */
    public $language = null;


    /**
     * Create a new Controller instance.
     */
    public function __construct()
    {
        parent::__construct();

        // Initialise the Language object.
        if ($this->language !== false) {
            $this->language = Language::instance();
        }

        // Setup the (legacy) Middleware.
        $this->beforeFilter('@callLegacyBefore');

        $this->afterFilter('@callLegacyAfter');
    }

    /**
     * Setup the Controller's parameters and method name.
     *
     * @param \Routing\Route $route
     * @return void
     */
    protected function setupController(Route $route, Request $request)
    {
        $action = $route->getAction();

        if (isset($action['controller'])) {
            list($class, $method) = explode('@', $action['controller']);

            $this->method = $method;
        }

        $this->params = $route->getParams();
    }

    /**
     *  Call the (legacy) Before Middleware.
     *
     * @return mixed
     */
    public function callLegacyBefore(Route $route, Request $request)
    {
        // Setup the Controller instance.
        $this->setupController($route, $request);

        // Execute the Controller's Before Middleware.
        return $this->before();
    }

    /**
     *  Call the (legacy) After Middleware.
     *
     * @return void
     */
    public function callLegacyAfter(Route $route, Request $request, $response)
    {
        // Execute the Controller's After Middleware.
        $this->after($response);
    }

    /**
     * The (legacy) Middleware called before the Action execution.
     *
     * @return mixed|void
     */
    protected function before()
    {
        //
    }

    /**
     * The (legacy) Middleware called after the Action execution.
     *
     * @param mixed $response
     *
     * @return void
     */
    protected function after($response)
    {
        //
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
        // If the response which is returned from the Controller's Action is null and we have
        // View instances on View's Legacy support, we will assume that we are on Legacy Mode.

        if (is_null($response)) {
            return $this->createLegacyResponse();
        }

        return parent::processResponse($response);
    }

    /**
     * Create a Response instance from the legacy View API and return it.
     *
     * @return \Http\Response
     */
    protected function createLegacyResponse()
    {
        $items = View::getItems();

        $headers = View::getHeaders();

        // Render the View instances to response.
        $response = '';

        foreach ($items as $item) {
            $response .= $item->render();
        }

        // Create a Response instance and return it.
        return new Response($response, 200, $headers);
    }

    /**
     * Return a translated string.
     *
     * @return string
     */
    protected function trans($str, $code = LANGUAGE_CODE)
    {
        if ($this->language instanceof Language) {
            return $this->language->get($str, $code);
        }

        return $str;
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

}
