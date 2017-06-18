<?php

namespace App\Console;

use Nova\Console\Scheduling\Schedule;
use Nova\Foundation\Console\Kernel as ConsoleKernel;


class Kernel extends ConsoleKernel
{
	/**
	 * The Forge commands provided by the application.
	 *
	 * @var array
	 */
	protected $commands = array();


	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Nova\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		$schedule->command('mailer:queue:flush')
			->everyMinute();
	}
}
