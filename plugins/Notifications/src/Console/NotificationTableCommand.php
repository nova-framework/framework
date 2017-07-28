<?php

namespace Notifications\Console;


use Nova\Console\Command;
use Nova\Filesystem\Filesystem;

use InvalidArgumentException;
use ReflectionClass;


class NotificationTableCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'notifications:table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a migration for the notifications table';

    /**
     * The filesystem instance.
     *
     * @var \Nova\Filesystem\Filesystem
     */
    protected $files;


    /**
     * Create a new notifications table command instance.
     *
     * @param  \Nova\Filesystem\Filesystem  $files
     * @param  mixed $composer
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
    public function fire()
    {
        $fullPath = $this->createBaseMigration();

        $this->files->put($fullPath, $this->files->get(__DIR__ .str_replace('/', DS, '/stubs/notifications.stub')));

        $this->info('Migration created successfully!');

        $this->call('optimize');
    }

    /**
     * Create a base migration file for the notifications.
     *
     * @return string
     */
    protected function createBaseMigration()
    {
        $path = $this->container['path'] .DS .'Database' .DS .'Migrations';

        return $this->container['migration.creator']->create('create_notifications_table', $path);
    }
}
