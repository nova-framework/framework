<?php


return array(
    /**
     * Debug Mode
     */
    'debug' => true, // When enabled the actual PHP errors will be shown.

    /**
     * The Website URL.
     */
    'url' => 'http://www.novaframework.dev/',

    /**
    * The Administrator's E-mail Address.
    */
    'email' => 'admin@novaframework.dev',

    /**
     * The Website Path.
     */
    'path' => '/',

    /**
     * Website Name.
     */
    'name' => 'Nova 4.0',

    /**
     * The default Theme.
     */
    'theme' => 'Bootstrap',

    /**
     * The Backend's Color Scheme.
     */
    'color_scheme' => 'blue',

    /**
     * The default locale that will be used by the translation.
     */
    'locale' => 'en',

    /**
     * The default Timezone for your website.
     * http://www.php.net/manual/en/timezones.php
     */
    'timezone' => 'Europe/London',

    /**
     * The Encryption Key.
     * This page can be used to generate key - http://novaframework.com/token-generator
     */
    'key' => 'SomeRandomStringThere_1234567890',

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log settings for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Settings: "single", "daily", "syslog", "errorlog"
    |
    */

    'log' => 'single',

    /**
     * The Application's Middleware stack.
     */
    'middleware' => array(
        'Nova\Foundation\Http\Middleware\CheckForMaintenanceMode',
        'Nova\Assets\Middleware\DispatchAssetFiles',
    ),

    /**
     * The Application's route Middleware Groups.
     */
    'middlewareGroups' => array(
        'web' => array(
            'Shared\Forensics\Middleware\HandleProfiling',
            'App\Middleware\EncryptCookies',
            'Nova\Cookie\Middleware\AddQueuedCookiesToResponse',
            'Nova\Session\Middleware\StartSession',
            'Nova\Localization\Middleware\SetupLanguage',
            'Nova\View\Middleware\ShareErrorsFromSession',
            'App\Middleware\VerifyCsrfToken',
            'App\Middleware\MarkNotificationAsRead',
        ),
        'api' => array(
            'throttle:60,1',
        )
    ),

    /**
     * The Application's route Middleware.
     */
    'routeMiddleware' => array(
        'auth'     => 'Nova\Auth\Middleware\Authenticate',
        'guest'    => 'App\Middleware\RedirectIfAuthenticated',
        'throttle' => 'Nova\Routing\Middleware\ThrottleRequests',
    ),

    /**
     * The registered Service Providers.
     */
    'providers' => array(
        'Nova\Auth\AuthServiceProvider',
        'Nova\Bus\BusServiceProvider',
        'Nova\Cache\CacheServiceProvider',
        'Nova\Assets\AssetServiceProvider',
        'Nova\Routing\RoutingServiceProvider',
        'Nova\Cookie\CookieServiceProvider',
        'Nova\Packages\PackageServiceProvider',
        'Nova\Database\DatabaseServiceProvider',
        'Nova\Encryption\EncryptionServiceProvider',
        'Nova\Filesystem\FilesystemServiceProvider',
        'Nova\Hashing\HashServiceProvider',
        'Nova\Mail\MailServiceProvider',
        'Nova\Pagination\PaginationServiceProvider',
        'Nova\Queue\QueueServiceProvider',
        'Nova\Redis\RedisServiceProvider',
        'Nova\Session\SessionServiceProvider',
        'Nova\Localization\LocalizationServiceProvider',
        'Nova\Validation\ValidationServiceProvider',
        'Nova\View\ViewServiceProvider',
        'Nova\Broadcasting\BroadcastServiceProvider',
        'Nova\Notifications\NotificationServiceProvider',

        // The Forge Providers.
        'Nova\Cache\ConsoleServiceProvider',
        'Nova\Foundation\Providers\ConsoleSupportServiceProvider',
        'Nova\Foundation\Providers\ForgeServiceProvider',
        'Nova\Database\MigrationServiceProvider',
        'Nova\Database\SeedServiceProvider',
        'Nova\Localization\ConsoleServiceProvider',
        'Nova\Packages\ConsoleServiceProvider',
        'Nova\Routing\ConsoleServiceProvider',
        'Nova\Session\ConsoleServiceProvider',

        // The Shared Providers.
        'Shared\Auth\Reminders\ReminderServiceProvider',
        'Shared\Auth\AuthServiceProvider',
        'Shared\Backup\BackupServiceProvider',
        'Shared\Cache\CacheServiceProvider',
        'Shared\DomPDF\PdfServiceProvider',
        'Shared\Queue\QueueServiceProvider',
        'Shared\Routing\RoutingServiceProvider',
        'Shared\Widgets\WidgetServiceProvider',

        // The Application Providers.
        'App\Providers\AppServiceProvider',
        'App\Providers\AuthServiceProvider',
        'App\Providers\EventServiceProvider',
        'App\Providers\RouteServiceProvider',
        'App\Providers\BroadcastServiceProvider',
    ),

    /**
     * The Service Providers Manifest path.
     */
    'manifest' => STORAGE_PATH .'framework',

    /**
     * The registered Class Aliases.
     */
    'aliases' => array(
        // The Support Classes.
        'Arr'           => 'Nova\Support\Arr',
        'Str'           => 'Nova\Support\Str',

        // The Database Seeder.
        'Seeder'        => 'Nova\Database\Seeder',

        // The Support Facades.
        'App'           => 'Nova\Support\Facades\App',
        'Asset'         => 'Nova\Support\Facades\Asset',
        'Auth'          => 'Nova\Support\Facades\Auth',
        'Broadcast'     => 'Nova\Support\Facades\Broadcast',
        'Bus'           => 'Nova\Support\Facades\Bus',
        'Cache'         => 'Nova\Support\Facades\Cache',
        'Config'        => 'Nova\Support\Facades\Config',
        'Cookie'        => 'Nova\Support\Facades\Cookie',
        'Crypt'         => 'Nova\Support\Facades\Crypt',
        'DB'            => 'Nova\Support\Facades\DB',
        'Event'         => 'Nova\Support\Facades\Event',
        'File'          => 'Nova\Support\Facades\File',
        'Forge'         => 'Nova\Support\Facades\Forge',
        'Gate'          => 'Nova\Support\Facades\Gate',
        'Hash'          => 'Nova\Support\Facades\Hash',
        'Input'         => 'Nova\Support\Facades\Input',
        'Language'      => 'Nova\Support\Facades\Language',
        'Mailer'        => 'Nova\Support\Facades\Mailer',
        'Notification'  => 'Nova\Support\Facades\Notification',
        'Paginator'     => 'Nova\Support\Facades\Paginator',
        'Queue'         => 'Nova\Support\Facades\Queue',
        'Redirect'      => 'Nova\Support\Facades\Redirect',
        'Redis'         => 'Nova\Support\Facades\Redis',
        'Request'       => 'Nova\Support\Facades\Request',
        'Response'      => 'Nova\Support\Facades\Response',
        'Route'         => 'Nova\Support\Facades\Route',
        'Schedule'      => 'Nova\Support\Facades\Schedule',
        'Schema'        => 'Nova\Support\Facades\Schema',
        'Session'       => 'Nova\Support\Facades\Session',
        'Validator'     => 'Nova\Support\Facades\Validator',
        'Log'           => 'Nova\Support\Facades\Log',
        'URL'           => 'Nova\Support\Facades\URL',
        'Template'      => 'Nova\Support\Facades\Template',
        'View'          => 'Nova\Support\Facades\View',
        'Package'       => 'Nova\Support\Facades\Package',

        // The Shared Facades.
        'Action'        => 'Shared\Support\Facades\Action',
        'Filter'        => 'Shared\Support\Facades\Filter',
        'PDF'           => 'Shared\Support\Facades\PDF',
        'Password'      => 'Shared\Support\Facades\Password',
        'Widget'        => 'Shared\Support\Facades\Widget',

        // The Forensics Console.
        'Console'       => 'Shared\Forensics\Console',
    ),

);
