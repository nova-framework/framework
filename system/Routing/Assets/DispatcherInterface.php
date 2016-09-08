<?php

namespace Routing\Assets;

use Http\Request;

use Symfony\Component\HttpFoundation\Response;


interface DispatcherInterface
{

    /**
     * Dispatch a Assets File Response.
     *
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function dispatch(Request $request);

    /**
     * Serve a File.
     *
     * @param string $path
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function serve($path, Request $request);

}
