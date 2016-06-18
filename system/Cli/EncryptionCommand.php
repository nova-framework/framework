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

        $this->makeKey($this->length);

        $output->writeln("<info>An Encryption key has been generated.</>");
    }

    public function makeKey($length)
    {
        $key = str_random($length);

        $file = file_get_contents("app/Config/App.php");

        $file = str_replace("    'key' => 'SomeRandomString______1234567890',", "    'key' => '$key',", $file);

        file_put_contents("app/Config/App.php", $file);
    }
}
