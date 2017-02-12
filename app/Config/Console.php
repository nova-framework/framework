<?php
/**
 * Console Application Configuration.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */


return array(

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => array(
        'Nova\Foundation\Providers\ForgeServiceProvider',
        'Nova\Foundation\Providers\ConsoleSupportServiceProvider',
        'Nova\Auth\Reminders\ConsoleServiceProvider',
        'Nova\Cache\ConsoleServiceProvider',
        'Nova\Database\MigrationServiceProvider',
        'Nova\Database\SeedServiceProvider',
        'Nova\Log\ConsoleServiceProvider',
        'Nova\Module\Providers\ConsoleServiceProvider',
        'Nova\Module\Providers\GeneratorServiceProvider',
        'Nova\Plugin\Providers\ConsoleServiceProvider',
        'Nova\Plugin\Providers\GeneratorServiceProvider'
        'Nova\Session\ConsoleServiceProvider',
        'Nova\Routing\ControllerServiceProvider',
        'Nova\View\ConsoleServiceProvider',
    ),
);
