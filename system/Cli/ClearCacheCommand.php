<?php
namespace Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
        $cachePath = 'app/Storage/Cache';
        $cache = glob($cachePath.'/*');
        $error = false;

        if (is_dir($cachePath)) {
            $this->clearCache($cache);
            $output->writeln("<info>The cache has been cleared.</>");
        } else {
            $output->writeln("<error>Cache directory does not exist.</>");
            $error = true;
        }
    }

    public function clearCache($files)
    {
        foreach($files as $file){
          if(is_file($file)) {
            unlink($file);
          }
        }
    }
}
