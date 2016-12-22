<?php

namespace Shared\Database\Backup\Databases;

use Shared\Database\Backup\Console;


class PostgresDatabase implements DatabaseInterface
{
    protected $console;
    protected $database;
    protected $user;
    protected $password;
    protected $host;

    public function __construct(Console $console, $database, $user, $password, $host)
    {
        $this->console  = $console;
        $this->database = $database;
        $this->user     = $user;
        $this->password = $password;
        $this->host     = $host;
    }

    public function dump($destinationFile)
    {
        $command = sprintf('PGPASSWORD=%s pg_dump -Fc --no-acl --no-owner -h %s -U %s %s > %s',
            escapeshellarg($this->password),
            escapeshellarg($this->host),
            escapeshellarg($this->user),
            escapeshellarg($this->database),
            escapeshellarg($destinationFile)
        );

        return $this->console->run($command);
    }

    public function restore($sourceFile)
    {
        $command = sprintf('PGPASSWORD=%s pg_restore --verbose --clean --no-acl --no-owner -h %s -U %s -d %s %s',
            escapeshellarg($this->password),
            escapeshellarg($this->host),
            escapeshellarg($this->user),
            escapeshellarg($this->database),
            escapeshellarg($sourceFile)
        );

        return $this->console->run($command);
    }

    public function getDatabase()
    {
        return $this->database;
    }
    
    public function getFileExtension()
    {
        return 'dump';
    }
}
