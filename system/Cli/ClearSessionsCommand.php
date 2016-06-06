<?php
namespace Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Core\Config;

class ClearSessionsCommand extends Command
{

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
        $error = false;
        $path = Config::get('session.files', 'app/Storage/Sessions');

        if ($input->getArgument('lifeTime')) {
            $lifeTime = $input->getArgument('lifeTime');
        } else {
            $lifeTime = Config::get('session.lifetime', 180);
        }

        if (!is_dir($path)) {
            $output->writeln("<error>Session directory does not exist. path: $path</>");
            $error = true;
        }

        self::clearSessions($path, $lifeTime);
        $output->writeln("<info>The sessions have been cleared. Lifetime: $lifeTime, path: $path</>");
    }

    protected function clearSessions($path, $lifeTime)
    {
        foreach (glob($path .'/sess_*') as $file) {
            clearstatcache(true, $file);

            if (((filemtime($file) + $lifeTime) < time()) && file_exists($file)) {
                unlink($file);
            }
        }
    }
}
