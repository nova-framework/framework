<?php

namespace Shared\DomPDF;

use Nova\Support\ServiceProvider as BaseServiceProvider;

use Shared\DomPDF\PDF;

use Dompdf\Dompdf;

use Exception;


class ServiceProvider extends BaseServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;


    /**
     * Register the service provider.
     *
     * @throws \Exception
     * @return void
     */
    public function register()
    {
        $this->app->bind('dompdf.options', function ()
        {
            $defines = $this->app['config']->get('dompdf.defines');

            if (empty($defines)) {
                $options = $this->app['config']->get('dompdf.options');
            } else {
                $options = array();

                foreach ($defines as $key => $value) {
                    $key = strtolower(str_replace('DOMPDF_', '', $key));

                    $options[$key] = $value;
                }
            }

            return $options;
        });

        $this->app->bind('dompdf', function ()
        {
            $options = $this->app->make('dompdf.options');

            $dompdf = new Dompdf($options);

            $dompdf->setBasePath(realpath(base_path('webroot')));

            return $dompdf;
        });

        $this->app->alias('dompdf', Dompdf::class);

        $this->app->bind('dompdf.wrapper', function ($app)
        {
            if (! $app['files']->exists($path = storage_path('fonts'))) {
                $app['files']->makeDirectory($path, 0755, true, true);
            }

            return new PDF($app['dompdf'], $app['config'], $app['files'], $app['view']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('dompdf', 'dompdf.options', 'dompdf.wrapper');
    }
}
