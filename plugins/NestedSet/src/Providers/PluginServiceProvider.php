<?php

namespace NestedSet\Providers;

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
		$this->package('NestedSet', 'nested_set', $path);

		//
	}

	/**
	 * Register the NestedSet plugin Service Provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

}
