<?php

namespace Shared\Notifications\Console;

use Nova\Modules\Console\MakeCommand;

use Symfony\Component\Console\Input\InputArgument;


class ModuleNotificationMakeCommand extends MakeCommand
{
    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'make:module:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Module Notification class';

    /**
     * String to store the command type.
     *
     * @var string
     */
    protected $type = 'Notification';

    /**
     * Plugin folders to be created.
     *
     * @var array
     */
    protected $listFolders = array(
        'Notifications/',
    );

    /**
     * Plugin files to be created.
     *
     * @var array
     */
    protected $listFiles = array(
        '{{filename}}.php',
    );

    /**
     * Plugin stubs used to populate defined files.
     *
     * @var array
     */
    protected $listStubs = array(
        'default' => array(
            'notification.stub',
        ),
    );

    /**
     * Resolve Container after getting file path.
     *
     * @param string $filePath
     *
     * @return array
     */
    protected function resolveByPath($filePath)
    {
        $this->data['filename']  = $this->makeFileName($filePath);

        $this->data['namespace'] = $this->getBaseNamespace() .'\\' .$this->getNamespace($filePath);

        $this->data['className'] = basename($filePath);
    }

    /**
     * Replace placeholder text with correct values.
     *
     * @return string
     */
    protected function formatContent($content)
    {
        $searches = array(
            '{{filename}}',
            '{{namespace}}',
            '{{className}}',
        );

        $replaces = array(
            $this->data['filename'],
            $this->data['namespace'],
            $this->data['className'],
        );

        return str_replace($searches, $replaces, $content);
    }

    /**
     * Get stub content by key.
     *
     * @param int $key
     *
     * @return string
     */
    protected function getStubContent($stubName)
    {
        $content = $this->files->get(__DIR__ .DS .'stubs' .DS .$stubName);

        return $this->formatContent($content);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('slug', InputArgument::REQUIRED, 'The slug of the Module.'),
            array('name', InputArgument::REQUIRED, 'The name of the Notification class.'),
        );
    }
}
