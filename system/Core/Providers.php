<?php
/**
 * Providers - Implements a Service Provider Repository.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Core;

use Core\Application;
use Support\ServiceProvider;


class Providers
{
    /**
     * The path to the manifest.
     *
     * @var string
     */
    protected $manifestPath;

    /**
     * Create a new service repository instance.
     *
     * @param  string  $manifestPath
     * @return void
     */
    public function __construct($manifestPath)
    {
        $this->manifestPath = $manifestPath;
    }

    /**
     * Register the application service providers.
     *
     * @param  array  $providers
     * @param  string  $path
     * @return void
     */
    public function load(Application $app, array $providers)
    {
        $manifest = $this->loadManifest();

        if ($this->shouldRecompile($manifest, $providers)) {
            $manifest = $this->compileManifest($app, $providers);
        }

        foreach ($manifest['eager'] as $provider) {
            $app->register($this->createProvider($app, $provider));
        }

        $app->setDeferredServices($manifest['deferred']);
    }

    /**
     * Compile the application manifest file.
     *
     * @param  \Foundation\Application  $app
     * @param  array  $providers
     * @return array
     */
    protected function compileManifest(Application $app, $providers)
    {
        $manifest = $this->freshManifest($providers);

        foreach ($providers as $provider) {
            $instance = $this->createProvider($app, $provider);

            if ($instance->isDeferred()) {
                foreach ($instance->provides() as $service) {
                    $manifest['deferred'][$service] = $provider;
                }
            } else {
                $manifest['eager'][] = $provider;
            }
        }

        return $this->writeManifest($manifest);
    }

    /**
     * Create a new Service Provider instance.
     *
     * @param  \Foundation\Application  $app
     * @param  string  $provider
     * @return \Support\ServiceProvider
     */
    public function createProvider(Application $app, $provider)
    {
        return new $provider($app);
    }

    /**
     * Determine if the manifest should be compiled.
     *
     * @param  array  $manifest
     * @param  array  $providers
     * @return bool
     */
    public function shouldRecompile($manifest, $providers)
    {
        return is_null($manifest) || $manifest['providers'] != $providers;
    }

    /**
     * Load the service provider manifest JSON file.
     *
     * @return array
     */
    public function loadManifest()
    {
        $path = $this->manifestPath .'/services.json';

        if (file_exists($path)) {
            $manifest = json_decode(file_get_contents($path), true);

            return $manifest;
        }
    }

    /**
     * Write the service manifest file to disk.
     *
     * @param  array  $manifest
     * @return array
     */
    public function writeManifest($manifest)
    {
        $path = $this->manifestPath .'/services.json';

        file_put_contents($path, json_encode($manifest));

        return $manifest;
    }

    /**
     * Create a fresh manifest array.
     *
     * @param  array  $providers
     * @return array
     */
    protected function freshManifest(array $providers)
    {
        list($eager, $deferred) = array(array(), array());

        return compact('providers', 'eager', 'deferred');
    }
}
