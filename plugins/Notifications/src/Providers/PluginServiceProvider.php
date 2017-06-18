<?php

namespace Notifications\Providers;

use Nova\Foundation\AliasLoader;
use Nova\Support\ServiceProvider;

use Notifications\Console\NotificationMakeCommand;
use Notifications\Console\NotificationTableCommand;
use Notifications\Console\PluginNotificationMakeCommand;
use Notifications\Contracts\DispatcherInterface;
use Notifications\ChannelManager;


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
		$this->package('Notifications', 'notifications', $path);

		//
	}

	/**
	 * Register the Notifications plugin Service Provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerChannelManager();

		$this->registerFacades();

		$this->registerCommands();
	}

	protected function registerChannelManager()
	{
		$this->app->singleton(ChannelManager::class, function ($app)
		{
			return new ChannelManager($app, $app['events']);
		});

		$this->app->alias(
			ChannelManager::class, DispatcherInterface::class
		);
	}

	protected function registerFacades()
	{
		$loader = AliasLoader::getInstance();

		$loader->alias('Notification', 'Notifications\Support\Facades\Notification');
	}

	protected function registerCommands()
	{
		$this->app->singleton('command.notification.table', function ($app) {
			return new NotificationTableCommand($app['files']);
		});

		$this->app->singleton('command.notification.make', function ($app)
		{
			return new NotificationMakeCommand($app['files']);
		});

		$this->app->singleton('command.make.plugin.notification', function ($app)
		{
			return new PluginNotificationMakeCommand($app['files'], $app['plugins']);
		});

		$this->commands(
			'command.notification.make', 'command.notification.table', 'command.make.plugin.notification'
		);
	}
}
