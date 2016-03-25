<?php
namespace Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Helpers\ReservedWords;

class ModelCommand extends Command
{
    private $modelName;
    private $methods;

    protected function configure()
    {
        $this
            ->setName('make:model')
            ->setDescription('Create a model')
            ->addArgument(
                'modelName',
                InputArgument::REQUIRED,
                'What do you want the model to be called?'
            )
             ->addArgument(
                'methods',
                InputArgument::IS_ARRAY,
                'What methods do you want (separate multiple method with a space)?'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->modelName = $input->getArgument('modelName');
        $this->methods = $input->getArgument('methods');

        $error = null;
        if (in_array($this->modelName, ReservedWords::getList())) {
            $output->writeln("<error>Model name cannot be a reserved word</>");
            $error = true;
        }

        if (is_array($this->methods)) {
            foreach ($this->methods as $method) {
                if (in_array($method, ReservedWords::getList())) {
                    $output->writeln("<error>Method name ($method) cannot be a reserved word</>");
                    $error = true;
                }
            }
        }

        if ($error == true) {
            exit;
        }

        $this->makeModel();

        $output->writeln("<info>Model ".$this->modelName." created with ".count($this->methods)." methods</>");
    }

    public function makeModel()
    {

$data = "<?php
namespace App\Models;

use Core\Model;

class ".ucwords($this->modelName)." extends Model
{
    public function __construct()
    {
        parent::__construct();
    }
    ";

if (is_array($this->methods)) {
    foreach ($this->methods as $method) {
    $data .="
    public function $method()
    {

    }\n";
    }
}
$data .="}
";
        file_put_contents("app/Models/".ucwords($this->modelName).".php", $data);
    }
}
