<?php

namespace View\Engines;

use Closure;


class EngineResolver
{

    /**
     * The array of Engine resolvers.
     *
     * @var array
     */
    protected $resolvers = array();

    /**
     * The resolved Engine instances.
     *
     * @var array
     */
    protected $resolved = array();

    /**
     * Register a new Engine Resolver.
     *
     * The Engine string typically corresponds to a file extension.
     *
     * @param  string   $engine
     * @param  Closure  $resolver
     * @return void
     */
    public function register($engine, Closure $resolver)
    {
        $this->resolvers[$engine] = $resolver;
    }

    /**
     * Resolve an Engine instance by name.
     *
     * @param  string  $engine
     * @return \View\Engines\EngineInterface
     */
    public function resolve($engine)
    {
        if (! isset($this->resolved[$engine])) {
            $resolver = $this->resolvers[$engine];

            $this->resolved[$engine] = call_user_func($resolver);
        }

        return $this->resolved[$engine];
    }

}
