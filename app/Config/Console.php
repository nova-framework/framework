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
        'Nova\Database\Providers\MigrationServiceProvider',
        'Nova\Database\Providers\SeedServiceProvider',
        'Nova\Log\ConsoleServiceProvider',
        'Nova\Module\Providers\ConsoleServiceProvider',
        'Nova\Module\Providers\GeneratorServiceProvider',
        'Nova\Plugin\Providers\ConsoleServiceProvider',
        'Nova\Plugin\Providers\GeneratorServiceProvider',
        'Nova\Routing\ConsoleServiceProvider',
        'Nova\Session\ConsoleServiceProvider',
        'Nova\View\ConsoleServiceProvider',
    ),
);
