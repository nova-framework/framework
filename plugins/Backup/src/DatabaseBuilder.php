<?php

namespace Backup;

use Backup\Console;


class DatabaseBuilder
{
    protected $database;

    protected $console;


    public function __construct()
    {
        $this->console = new Console();
    }

    public function getDatabase(array $config)
    {
        $this->buildMySQL($config);

        return $this->database;
    }

    protected function buildMySQL(array $config)
    {
        $port = isset($config['port']) ? $config['port'] : 3306;

        $this->database = new Databases\MySQLDatabase(
            $this->console,
            $config['database'],
            $config['username'],
            $config['password'],
            $config['host'],
            $port
        );
    }
}
