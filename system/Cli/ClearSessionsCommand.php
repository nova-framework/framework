<?php
namespace Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ClearSessionsCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('clear:sessions')
            ->setDescription('Clears the sessions folder')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sessionsPath = 'app/Storage/Sessions';
        $sessions = glob($sessionsPath.'/*');
        $error = false;

        if (is_dir($sessionsPath)) {
            $this->clearSessions($sessions);
            $output->writeln("<info>The sessions folder has been cleared.</>");
        } else {
            $output->writeln("<error>Session directory does not exist.</>");
            $error = true;
        }
    }

    public function clearSessions($files)
    {
        foreach($files as $file){
          if(is_file($file)) {
            unlink($file);
          }
        }
    }
}
