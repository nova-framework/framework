<?php

namespace Nova\Routing\Assets;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;


interface DispatcherInterface
{

    /**
     * Dispatch a Assets File Response.
     *
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function dispatch(SymfonyRequest $request);

    /**
     * Serve a File.
     *
     * @param string $path
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function serve($path, SymfonyRequest $request);

}
