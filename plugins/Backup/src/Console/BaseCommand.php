<?php

namespace Backup\Console;

use Nova\Console\Command;
use Nova\Support\Facades\Config;

use Backup\DatabaseBuilder;
use Backup\Console;


class BaseCommand extends Command
{
	protected $databaseBuilder;

	protected $console;


	public function __construct(DatabaseBuilder $databaseBuilder)
	{
		parent::__construct();

		$this->databaseBuilder = $databaseBuilder;

		$this->console = new Console();
	}

	public function getDatabase($database)
	{
		$database = $database ?: Config::get('database.default');

		$realConfig = Config::get('database.connections.' .$database);

		return $this->databaseBuilder->getDatabase($realConfig);
	}

	protected function getDumpsPath()
	{
		$path = Config::get('backup::config.path');

		return rtrim($path, '\\/') .DS;
	}

	public function enableCompression()
	{
		return Config::set('backup::config.compress', true);
	}

	public function disableCompression()
	{
		return Config::set('backup::config.compress', false);
	}

	public function isCompressionEnabled()
	{
		return Config::get('backup::config.compress');
	}

	public function isCompressed($fileName)
	{
		return (pathinfo($fileName, PATHINFO_EXTENSION) === "gz");
	}
}
