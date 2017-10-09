<?php

namespace Shared\Notifications\Console;

use Nova\Console\GeneratorCommand;


class NotificationMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Notification class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Notification';


    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ .str_replace('/', DS, '/stubs/notification.stub');
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace .'\Notifications';
    }
}
