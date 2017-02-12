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
        // The Framework Providers.
        'Nova\Plugin\PluginServiceProvider',
        'Nova\Module\ModuleServiceProvider',
        'Nova\Auth\AuthServiceProvider',
        'Nova\Cache\CacheServiceProvider',
        'Nova\Routing\RoutingServiceProvider',
        'Nova\Cookie\CookieServiceProvider',
        'Nova\Database\DatabaseServiceProvider',
        'Nova\Encryption\EncryptionServiceProvider',
        'Nova\Filesystem\FilesystemServiceProvider',
        'Nova\Foundation\Providers\FoundationServiceProvider',
        'Nova\Hashing\HashServiceProvider',
        'Nova\Language\LanguageServiceProvider',
        'Nova\Log\LogServiceProvider',
        'Nova\Mail\MailServiceProvider',
        'Nova\Database\MigrationServiceProvider',
        'Nova\Pagination\PaginationServiceProvider',
        'Nova\Queue\QueueServiceProvider',
        'Nova\Redis\RedisServiceProvider',
        'Nova\Auth\Reminders\ReminderServiceProvider',
        'Nova\Database\SeedServiceProvider',
        'Nova\Session\SessionServiceProvider',
        'Nova\Validation\ValidationServiceProvider',
        'Nova\View\ViewServiceProvider',
        'Nova\Widget\WidgetServiceProvider',
        'Nova\Assets\AssetServiceProvider',

        // The Console Providers.
        'Nova\Foundation\Providers\ForgeServiceProvider',
        'Nova\Session\CommandsServiceProvider',
        'Nova\Foundation\Providers\ConsoleSupportServiceProvider',
        'Nova\Routing\ControllerServiceProvider',

        // The Application Providers.
        'App\Providers\AppServiceProvider',
        'App\Providers\AuthServiceProvider',
        'App\Providers\EventServiceProvider',
        'App\Providers\RouteServiceProvider',
    ),
);
