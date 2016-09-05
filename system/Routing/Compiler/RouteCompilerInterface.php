<?php

namespace Routing\Compiler;

use Routing\Route;


interface RouteCompilerInterface
{
    /**
     * Compiles the current Route instance.
     *
     * @param Route $route A Route instance
     * @param array $optionals The optional paramters
     *
     * @return CompiledRoute A CompiledRoute instance
     *
     * @throws \LogicException If the Route cannot be compiled because the path or host pattern is invalid
     */
    public function compile(Route $route, array $optionals = array());
}
