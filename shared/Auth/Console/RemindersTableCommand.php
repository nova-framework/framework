<?php

namespace Shared\Auth\Console;

use Nova\Console\Command;
use Nova\Filesystem\Filesystem;
use Nova\Support\Str;

use Symfony\Component\Console\Input\InputArgument;

use InvalidArgumentException;


class RemindersTableCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'auth:reminders-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a migration for the password reminders table';

    /**
     * The filesystem instance.
     *
     * @var \Nova\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new reminder table command instance.
     *
     * @param  \Nova\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $config = $this->container['config'];

        $brokers = array_keys(
            $config->get('reminders::reminders', array())
        );

        $name = $this->argument('name');

        if (empty($name)) {
            $name = $config->get('reminders::default', 'users');
        } else if (! array_key_exists($name, $brokers)) {
            return $this->error('Password broker does not exist.');
        }

        $table = $config->get("reminders::reminders.{$name}.table", 'password_reminders');

        $name = 'create_' .$name .'_password_reminders_table';

        //
        $fullPath = $this->createBaseMigration($name);

        $this->files->put($fullPath, $this->getMigrationStub($name, $table));

        $this->info('Migration created successfully!');

        $this->call('optimize');
    }

    /**
     * Create a base migration file for the reminders.
     *
     * @param  string  $name
     *
     * @return string
     */
    protected function createBaseMigration($name)
    {
        $path = $this->container['path'] .DS .'Database' .DS .'Migrations';

        return $this->container['migration.creator']->create($name, $path);
    }

    /**
     * Get the contents of the reminder migration stub.
     *
     * @param  string  $name
     * @param  string  $table
     *
     * @return string
     */
    protected function getMigrationStub($name, $table)
    {
        $className = Str::studly($name);

        // Get the stub contents.
        $stubPath = $this->getStub();

        $stub = $this->files->get($stubPath);

        // Replace the class and table names into stub content.
        $searches = array('CreatePasswordRemindersTable', 'password_reminders');

        $replaces = array($className, $table);

        return str_replace($searches, $replaces, $stub);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return realpath(__DIR__) .str_replace('/', DS, '/stubs/reminders.stub');
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
