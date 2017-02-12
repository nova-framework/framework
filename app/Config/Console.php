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
    | Console Service Providers
    |--------------------------------------------------------------------------
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
        'Nova\Plugin\Providers\GeneratorServiceProvider',
        'Nova\Routing\ControllerServiceProvider',
        'Nova\Session\ConsoleServiceProvider',
        'Nova\View\ConsoleServiceProvider',
    ),
);
