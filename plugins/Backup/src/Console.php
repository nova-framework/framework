<?php

namespace Backup;

use Symfony\Component\Process\Process;


class Console
{
	public function run($command)
	{
		$process = new Process($command);

		$process->setTimeout(999999999);

		$process->run();

		if ($process->isSuccessful()) {
			return true;
		}

		return $process->getErrorOutput();
	}
}
