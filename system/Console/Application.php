<?php

namespace Console;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command as SymfonyCommand;


class Application extends \Symfony\Component\Console\Application
{
    /**
     * The exception handler instance.
     *
     * @var \Exception\Handler
     */
    protected $exceptionHandler;

    /**
     * The Nova Application instance.
     *
     * @var \Foundation\Application
     */
    protected $framework;

    /**
     * Create and boot a new Console application.
     *
     * @param  \Foundation\Application  $app
     * @return \Console\Application
     */
    public static function start($app)
    {
        return static::make($app)->boot();
    }

    /**
     * Create a new Console application.
     *
     * @param  \Foundation\Application  $app
     * @return \Console\Application
     */
    public static function make($app)
    {
        $app->boot();

        $console = with($console = new static('Nova Framework', $app::VERSION))
                                ->serFramework($app)
                                ->setExceptionHandler($app['exception'])
                                ->setAutoExit(false);

        $app->instance('artisan', $console);

        return $console;
    }

    /**
     * Boot the Console application.
     *
     * @return \Console\Application
     */
    public function boot()
    {
        require $this->framework['path'].'/start/artisan.php';

        if (isset($this->framework['events'])) {
            $this->framework['events']
                    ->fire('nova.console.start', array($this));
        }

        return $this;
    }

    /**
     * Run an Artisan console command by name.
     *
     * @param  string  $command
     * @param  array   $parameters
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return void
     */
    public function call($command, array $parameters = array(), OutputInterface $output = null)
    {
        $parameters['command'] = $command;

        $output = $output ?: new NullOutput;

        $input = new ArrayInput($parameters);

        return $this->find($command)->run($input, $output);
    }

    /**
     * Add a command to the console.
     *
     * @param  \Symfony\Component\Console\Command\Command  $command
     * @return \Symfony\Component\Console\Command\Command
     */
    public function add(SymfonyCommand $command)
    {
        if ($command instanceof Command) {
            $command->serFramework($this->framework);
        }

        return $this->addToParent($command);
    }

    /**
     * Add the command to the parent instance.
     *
     * @param  \Symfony\Component\Console\Command\Command  $command
     * @return \Symfony\Component\Console\Command\Command
     */
    protected function addToParent(SymfonyCommand $command)
    {
        return parent::add($command);
    }

    /**
     * Add a command, resolving through the application.
     *
     * @param  string  $command
     * @return \Symfony\Component\Console\Command\Command
     */
    public function resolve($command)
    {
        return $this->add($this->framework[$command]);
    }

    /**
     * Resolve an array of commands through the application.
     *
     * @param  array|dynamic  $commands
     * @return void
     */
    public function resolveCommands($commands)
    {
        $commands = is_array($commands) ? $commands : func_get_args();

        foreach ($commands as $command) {
            $this->resolve($command);
        }
    }

    /**
     * Get the default input definitions for the applications.
     *
     * @return \Symfony\Component\Console\Input\InputDefinition
     */
    protected function getDefaultInputDefinition()
    {
        $definition = parent::getDefaultInputDefinition();

        $definition->addOption($this->getEnvironmentOption());

        return $definition;
    }

    /**
     * Get the global environment option for the definition.
     *
     * @return \Symfony\Component\Console\Input\InputOption
     */
    protected function getEnvironmentOption()
    {
        $message = 'The environment the command should run under.';

        return new InputOption('--env', null, InputOption::VALUE_OPTIONAL, $message);
    }

    /**
     * Render the given exception.
     *
     * @param  \Exception  $e
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return void
     */
    public function renderException($e, $output)
    {
        if (isset($this->exceptionHandler)) {
            $this->exceptionHandler->handleConsole($e);
        }

        parent::renderException($e, $output);
    }

    /**
     * Set the exception handler instance.
     *
     * @param  \Exception\Handler  $handler
     * @return \Console\Application
     */
    public function setExceptionHandler($handler)
    {
        $this->exceptionHandler = $handler;

        return $this;
    }

    /**
     * Set the Laravel application instance.
     *
     * @param  \Foundation\Application  $app
     * @return \Console\Application
     */
    public function serFramework($app)
    {
        $this->framework = $app;

        return $this;
    }

    /**
     * Set whether the Console app should auto-exit when done.
     *
     * @param  bool  $boolean
     * @return \Console\Application
     */
    public function setAutoExit($boolean)
    {
        parent::setAutoExit($boolean);

        return $this;
    }

}
