<?php
/*namespace Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Helpers\ReservedWords;

class InstallerCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('install')
            ->setDescription('Install Framework')
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'What is the project path? relative to the web root for example http://localhost/framework would be framework'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');

        //if $path does not start with a / then wrap it with /$path/
        if ($path[0] != '/') {
            $path = '/'.$path.'/';
        }
        //if the end of $path is not / then add it
        if (substr($path, -1) !='/') {
            $path = $path.'/';
        }

        //setup .htaccess
        $file = "public/.htaccess";
        $content = null;
        $content = file_get_contents($file);
        $content = preg_replace("/.+RewriteBase.+\n/", "  RewriteBase $path\n", $content);
        file_put_contents($file, $content);

        //setup Config.example.php
        $file = "system/Core/Config.example.php";
        if (file_exists($file)) {
            $newfile = "system/Core/Config.php";
            $content = null;
            $content = file_get_contents($file);
            $content = preg_replace("/.+DIR.+\n/", "        define('DIR', '$path');\n", $content);
            file_put_contents($newfile, $content);
            unlink($file);
        }

        //setup Config.php
        $file = "system/Core/Config.php";
        if (file_exists($file)) {
            $content = null;
            $content = file_get_contents($file);
            $content = preg_replace("/.+DIR.+\n/", "        define('DIR', '$path');\n", $content);
            file_put_contents($file, $content);
        }

        $output->writeln("<info>.htaccess configured</>");
        $output->writeln("<info>system/Core/Config.php configured</>");*/


        /*$helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Continue with this action?', false);

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        $question = new Question("Please enter the name of the bundle:\n");

        $bundle = $helper->ask($input, $output, $question);
        $output->writeln("<info>$bundle called.</>");

        //----- Multiple choice Question
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Please select your favorite color (defaults to red)',
            array('red', 'blue', 'yellow'),
            0
        );
        $question->setErrorMessage('Color %s is invalid.');

        $color = $helper->ask($input, $output, $question);
        $output->writeln('You have just selected: '.$color);



        $bundles = array('AcmeDemoBundle', 'AcmeBlogBundle', 'AcmeStoreBundle');
        $question = new Question('Please enter the name of a bundle', 'FooBundle');
        $question->setAutocompleterValues($bundles);

        $name = $helper->ask($input, $output, $question);*/
    //}
//}
