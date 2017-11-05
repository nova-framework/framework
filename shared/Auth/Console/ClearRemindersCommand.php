<?php

namespace Shared\Auth\Console;

use Nova\Console\Command;

use Symfony\Component\Console\Input\InputArgument;


class ClearRemindersCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'auth:clear-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flush expired reminders.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $name = $this->argument('name');

        $broker = $this->container['auth.password']->broker($name);

        //
        $broker->getRepository()->deleteExpired();

        $this->info('Expired reminders cleared!');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('name', InputArgument::OPTIONAL, 'The name of the password broker.'),
        );
    }
}
