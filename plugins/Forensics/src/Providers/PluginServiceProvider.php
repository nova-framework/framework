<?php

namespace Forensics\Providers;

use Nova\Support\ServiceProvider;


class PluginServiceProvider extends ServiceProvider
{

	/**
	 * Bootstrap the Application Events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$path = realpath(__DIR__ .'/../');

		// Configure the Package.
		$this->package('Forensics', 'forensics', $path);

		//
	}

	/**
	 * Register the Forensics plugin Service Provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

}
