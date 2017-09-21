<?php

namespace Shared\Database\Backup;

use Shared\Database\Backup\Console;

use Exception;


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
        $driver = $config['driver'];

        if ($driver == 'mysql') {
            return $this->buildMySQL($config);
        } else if ($driver == 'sqlite') {
            return $this->buildSqlite($config);
        } else if ($driver == 'pgsql') {
            return $this->buildPostgres($config);
        }

        throw new Exception('Database driver not supported yet.');
    }

    protected function buildMySQL(array $config)
    {
        $port = isset($config['port']) ? $config['port'] : 3306;

        return $this->database = new Databases\MySQLDatabase(
            $this->console,
            $config['database'],
            $config['username'],
            $config['password'],
            $config['hostname'],
            $port
        );
    }

    protected function buildSqlite(array $config)
    {
        return $this->database = new Databases\SqliteDatabase(
            $this->console,
            $config['database']
        );
    }

    protected function buildPostgres(array $config)
    {
        return $this->database = new Databases\PostgresDatabase(
            $this->console,
            $config['database'],
            $config['username'],
            $config['password'],
            $config['hostname']
        );
    }
}
