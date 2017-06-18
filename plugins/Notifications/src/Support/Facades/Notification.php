<?php

namespace Notifications\Support\Facades;

use Nova\Support\Facades\Facade;


/**
 * @see \Notifications\ChannelManager
 */
class Notification extends Facade
{
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'Notifications\ChannelManager';
	}
}
