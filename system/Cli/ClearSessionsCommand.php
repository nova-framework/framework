<?php
namespace Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ClearSessionsCommand extends Command
{

    private $savePath = 'app/Storage/Sessions/';

    protected function configure()
    {
        $this
            ->setName('clear:sessions')
            ->setDescription('Clears the sessions folder')
            ->addArgument(
                'lifeTime',
                InputArgument::OPTIONAL,
                'Number of minutes the Session is allowed to remain idle before it expires (default: 180).'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        if ($input->getArgument('lifeTime')) {
            $lifeTime = $input->getArgument('lifeTime');
        } else {
            $lifeTime = 180;
        }

        if (is_dir($this->savePath)) {
            self::clearSessions($lifeTime);
            $output->writeln("<info>The sessions have been cleared. Lifetime: $lifeTime</>");
        } else {
            $output->writeln("<error>Session directory does not exist.</>");
            $error = true;
        }
    }

    protected function clearSessions($lifeTime)
    {
        foreach (glob($this->savePath .'sess_*') as $file) {
            clearstatcache(true, $file);

            if (((filemtime($file) + $lifeTime) < time()) && file_exists($file)) {
                unlink($file);
            }
        }

        return true;
    }
}
