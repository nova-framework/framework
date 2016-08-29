<?php
namespace Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Config\Config;

class ClearCacheCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('clear:cache')
            ->setDescription('Clears the cache folder')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $error = false;
        $path = Config::get('cache.path', 'app/Storage/Cache');

        if (!is_dir($path)) {
            $output->writeln("<error>Cache directory does not exist. path: $path</>");
            $error = true;
        }

        self::cleanCache($path);
        $output->writeln("<info>Cache directory has been cleaned. path: $path</>");
    }

   protected function cleanCache($dir)
   {
       if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != ".." && $object != ".gitignore") {
                    if (is_dir($dir."/".$object)) {
                        self::cleanCache($dir."/".$object);
                    } else {
                        unlink($dir."/".$object);
                    }
                }
            }
            @rmdir($dir);
        }
    }
}
