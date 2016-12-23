<?php

namespace Shared\Database\Backup\Commands;

use Nova\Config\Config;

use Shared\Database\Backup\Commands\BaseCommand;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use File;


class BackupCommand extends BaseCommand
{
    protected $name = 'db:backup';

    protected $description = 'Backup the default database to `app/Database/Backup`';

    protected $filePath;
    protected $fileName;


    public function fire()
    {
        $config = $this->input->getOption('database');

        $database = $this->getDatabase($config);

        $this->checkDumpFolder();

        //
        $fileName = $this->argument('filename');

        if (! empty($fileName)) {
            $this->filePath = realpath($fileName);

            $this->fileName = basename($this->filePath);
        } else {
            $this->fileName = str_replace('_', '-', $database->getDatabase()) .'_' .date('Y-m-d_H-i-s') . '.' .$database->getFileExtension();

            $this->filePath = $this->getDumpsPath() . $this->fileName;
        }

        $status = $database->dump($this->filePath);

        if ($status === true) {
            if ($this->isCompressionEnabled()) {
                $this->compress();

                $this->fileName .= ".gz";
                $this->filePath .= ".gz";
            }

            if (! empty($fileName)) {
                $this->info(__d('shared', 'Database backup was successful. Saved to {0}', $this->filePath));
            } else {
                $this->info(__d('shared', 'Database backup was successful. {0} was saved in the dumps folder.', $this->fileName));
            }
        } else {
            $this->error(__d('shared', 'Database backup failed. {0}', $status));
        }
    }

    /**
     * Perform Gzip compression on file
     *
     * @return boolean      Status of command
     */
    protected function compress()
    {
        $command = sprintf('gzip -9 %s', $this->filePath);

        return $this->console->run($command);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('filename', InputArgument::OPTIONAL, 'Filename or -path for the dump.'),
        );
    }

    protected function getOptions()
    {
        return array(
            array('database', null, InputOption::VALUE_OPTIONAL, 'The database connection to backup'),
        );
    }

    protected function checkDumpFolder()
    {
        $dumpsPath = $this->getDumpsPath();

        if (! is_dir($dumpsPath)) {
            mkdir($dumpsPath);
        }
    }

}
