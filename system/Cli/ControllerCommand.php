<?php
namespace Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Helpers\ReservedWords;

class ControllerCommand extends Command
{
    private $controllerName;
    private $methods;

    protected function configure()
    {
        $this
            ->setName('make:controller')
            ->setDescription('Create a controller')
            ->addArgument(
                'controllerName',
                InputArgument::REQUIRED,
                'What do you want the controller to be called?'
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
        $this->controllerName = $input->getArgument('controllerName');
        $this->methods = $input->getArgument('methods');

        $error = null;
        if (in_array($this->controllerName, ReservedWords::getList())) {
            $output->writeln("<error>Controller name cannot be a reserved word</>");
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

        if (!is_dir("app/Views/".ucwords($this->controllerName))) {
             mkdir("app/Views/".ucwords($this->controllerName));
        }

        $this->makeViews();
        $this->makeRoutes();
        $this->makeController();

        $output->writeln("<info>Controller ".$this->controllerName." created with ".count($this->methods)." methods</>");
    }

    private function makeViews()
    {
        if (!is_dir("app/Views/".ucwords($this->controllerName))) {
            mkdir("app/Views/".ucwords($this->controllerName));
        }
        if (is_array($this->methods)) {
            foreach ($this->methods as $method) {
                file_put_contents("app/Views/".ucwords($this->controllerName)."/".ucwords($method.".php"), null);
            }
        }
    }

    public function makeRoutes()
    {
        $methods = null;
        if (is_array($this->methods)) {
            foreach ($this->methods as $method) {
                if ($method == 'index') {
                    $methods .="Router::any('".strtolower($this->controllerName)."', 'App\\Controllers\\".ucwords($this->controllerName)."@$method');\n";
                } else {
                    $methods .="Router::any('".strtolower($this->controllerName)."/$method', 'App\\Controllers\\".ucwords($this->controllerName)."@$method');\n";
                }
            }
            $file = file_get_contents("app/Routes.php");
            $file = str_replace("/** End default Routes */", "$methods/** End default Routes */", $file);
            file_put_contents("app/Routes.php", $file);
        }
    }

    public function makeController()
    {
$data = "<?php
namespace App\Controllers;

use Core\View;
use Core\Controller;

class ".ucwords($this->controllerName)." extends Controller
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
        \$data['title'] = '$method';

        View::renderTemplate('header', \$data);
        View::render('".ucwords($this->controllerName)."/".ucwords($method)."', \$data);
        View::renderTemplate('footer', \$data);
    }\n";
    }
}
$data .="}
";
        file_put_contents("app/Controllers/".ucwords($this->controllerName).".php", $data);
    }
}
