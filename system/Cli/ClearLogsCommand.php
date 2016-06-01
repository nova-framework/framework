<?php
namespace Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ClearLogsCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('clear:logs')
            ->setDescription('Clears the log files')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logsPath = 'app/Storage/Logs';
        $logs = glob($logsPath.'/*');
        $error = false;

        if (is_dir($logsPath)) {
            $this->clearLogs($logs);
            $output->writeln("<info>The log files have been cleared.</>");
        } else {
            $output->writeln("<error>Logs directory does not exist.</>");
            $error = true;
        }
    }

    public function clearLogs($files)
    {
        foreach($files as $file){
          if(is_file($file)) {
            $f = @fopen($file, "r+");
            if ($f !== false) {
                ftruncate($f, 0);
                fclose($f);
            }
          }
        }
    }
}
