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
        switch ($config['driver']) {
            case 'mysql':
                $this->buildMySQL($config);

                break;
            case 'sqlite':
                $this->buildSqlite($config);

                break;
            case 'pgsql':
                $this->buildPostgres($config);

                break;
            default:
                throw new \Exception('Database driver not supported yet');

                break;
        }

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

    protected function buildSqlite(array $config)
    {
        $this->database = new Databases\SqliteDatabase(
            $this->console,
            $config['database']
        );
    }

    protected function buildPostgres(array $config)
    {
        $this->database = new Databases\PostgresDatabase(
            $this->console,
            $config['database'],
            $config['username'],
            $config['password'],
            $config['host']
        );
    }

}
