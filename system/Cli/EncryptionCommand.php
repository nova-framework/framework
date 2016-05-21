<?php
namespace Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

require 'system/functions.php';

class EncryptionCommand extends Command
{
    private $length;

    protected function configure()
    {
        $this
            ->setName('make:key')
            ->setDescription('Generate an encryption key for the config file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->length = 32;

        $error = null;
        if (file_exists("app/Config.php")) {
            $this->makeKey($this->length);
            $output->writeln("<info>An Encryption key has been generated.</>");
        } else {
            $output->writeln("<error>No Config.php found, configure and rename Config.example.php to Config.php in app.</>");
            $error = true;
        }
    }

    public function makeKey($length)
    {
        $key = str_random($length);

        $file = file_get_contents("app/Config.php");
        $file = str_replace("define('ENCRYPT_KEY', '');", "define('ENCRYPT_KEY', '$key');", $file);
        file_put_contents("app/Config.php", $file);
    }
}
