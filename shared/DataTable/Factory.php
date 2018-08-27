<?php

namespace Shared\DataTable;

use Nova\Http\Request;
use Nova\Routing\ResponseFactory;

use Shared\DataTable\DataTable;


class Factory
{
    /**
     * The Request instance.
     *
     * @var \Nova\Http\Request
     */
    protected $request;

    /**
     * The Response Factory instance.
     *
     * @var \Nova\Routing\ResponseFactory
     */
    protected $responseFactory;


    /**
     * Create a new Widget Manager instance.
     *
     * @return void
     */
    public function __construct(Request $request, ResponseFactory $responseFactory)
    {
        $this->request = $request;

        $this->responseFactory = $responseFactory;
    }

    /**
     * Create a new DataTable instance.
     *
     * @param array $columns
     *
     * @return array
     */
    public function make(array $columns = array())
    {
        return new DataTable($this, $columns);
    }

    /**
     * Returns the Request instance.
     *
     * @return \Nova\Http\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Returns the Response Factory instance.
     *
     * @return \Nova\Routing\ResponseFactory
     */
    public function getResponseFactory()
    {
        return $this->responseFactory;
    }
}
