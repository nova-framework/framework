<?php

namespace Notifications;

use Nova\Bus\Contracts\ShouldQueueInterface;
use Nova\Bus\Command;

use Notifications\ChannelManager;


class SendQueuedNotifications extends Command implements ShouldQueueInterface
{
	/**
	 * The notifiable entities that should receive the notification.
	 *
	 * @var \Nova\Support\Collection
	 */
	protected $notifiables;

	/**
	 * The notification to be sent.
	 *
	 * @var \Notifications\Notification
	 */
	protected $notification;

	/**
	 * All of the channels to send the notification too.
	 *
	 * @var array
	 */
	protected $channels;


	/**
	 * Create a new job instance.
	 *
	 * @param  \Nova\Support\Collection  $notifiables
	 * @param  \Notifications\Notification  $notification
	 * @param  array  $channels
	 * @return void
	 */
	public function __construct($notifiables, $notification, array $channels = null)
	{
		$this->notifiables = $notifiables;

		$this->notification = $notification;

		$this->channels = $channels;
	}

	/**
	 * Send the notifications.
	 *
	 * @param  \Notifications\ChannelManager  $manager
	 * @return void
	 */
	public function handle(ChannelManager $manager)
	{
		$manager->sendNow($this->notifiables, $this->notification, $this->channels);
	}
}
