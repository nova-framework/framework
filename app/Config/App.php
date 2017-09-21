<?php
/**
 * Application Configuration
 *
 * @author David Carr - dave@daveismyname.com
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


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
    'name' => 'Nova 3.0',

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

    /**
     *  Prevents the website from CSRF attacks.
     */
    'csrf' => true,

    /**
     * The registered Service Providers.
     */
    'providers' => array(
        'Nova\Auth\AuthServiceProvider',
        'Nova\Cache\CacheServiceProvider',
        'Nova\Routing\RoutingServiceProvider',
        'Nova\Cookie\CookieServiceProvider',
        'Nova\Module\ModuleServiceProvider',
        'Nova\Database\DatabaseServiceProvider',
        'Nova\Encryption\EncryptionServiceProvider',
        'Nova\Filesystem\FilesystemServiceProvider',
        'Nova\Hashing\HashServiceProvider',
        'Nova\Log\LogServiceProvider',
        'Nova\Mail\MailServiceProvider',
        'Nova\Pagination\PaginationServiceProvider',
        'Nova\Redis\RedisServiceProvider',
        'Nova\Auth\Reminders\ReminderServiceProvider',
        'Nova\Session\SessionServiceProvider',
        'Nova\Language\LanguageServiceProvider',
        'Nova\Validation\ValidationServiceProvider',
        'Nova\Html\HtmlServiceProvider',
        'Nova\View\ViewServiceProvider',

        // The Forge Providers.
        'Nova\Auth\Reminders\ConsoleServiceProvider',
        'Nova\Cache\ConsoleServiceProvider',
        'Nova\Foundation\Providers\ConsoleSupportServiceProvider',
        'Nova\Foundation\Providers\ForgeServiceProvider',
        'Nova\Database\MigrationServiceProvider',
        'Nova\Database\SeedServiceProvider',
        'Nova\Module\Providers\ConsoleServiceProvider',
        'Nova\Module\Providers\GeneratorServiceProvider',
        'Nova\Routing\ConsoleServiceProvider',
        'Nova\Session\ConsoleServiceProvider',

        // The Shared Providers.
        'Shared\Database\Backup\ConsoleServiceProvider',
        'Shared\Routing\RoutingServiceProvider',

        // The Application Providers.
        'App\Providers\AppServiceProvider',
        'App\Providers\AuthServiceProvider',
        'App\Providers\EventServiceProvider',
        'App\Providers\RouteServiceProvider',
        'App\Providers\ThemeServiceProvider',
    ),

    /**
     * The Service Providers Manifest path.
     */
    'manifest' => ROOTDIR .'storage',

    /**
     * The registered Class Aliases.
     */
    'aliases' => array(
        // The Helpers.
        'Assets'        => 'Nova\Helpers\Assets',
        'Date'          => 'Nova\Helpers\Date',
        'Document'      => 'Nova\Helpers\Document',
        'Ftp'           => 'Nova\Helpers\Ftp',
        'GeoCode'       => 'Nova\Helpers\GeoCode',
        'Inflector'     => 'Nova\Helpers\Inflector',
        'Number'        => 'Nova\Helpers\Number',
        'ReservedWords' => 'Nova\Helpers\ReservedWords',
        'SimpleCurl'    => 'Nova\Helpers\SimpleCurl',
        'TableBuilder'  => 'Nova\Helpers\TableBuilder',
        'Tags'          => 'Nova\Helpers\Tags',

        // The Forensics Console.
        'Console'       => 'Nova\Forensics\Console',

        // The Support Classes.
        'Arr'           => 'Nova\Support\Arr',
        'Str'           => 'Nova\Support\Str',

        // The Database Seeder.
        'Seeder'        => 'Nova\Database\Seeder',

        // The Support Facades.
        'App'           => 'Nova\Support\Facades\App',
        'Auth'          => 'Nova\Support\Facades\Auth',
        'Cache'         => 'Nova\Support\Facades\Cache',
        'Config'        => 'Nova\Support\Facades\Config',
        'Cookie'        => 'Nova\Support\Facades\Cookie',
        'Crypt'         => 'Nova\Support\Facades\Crypt',
        'DB'            => 'Nova\Support\Facades\DB',
        'Event'         => 'Nova\Support\Facades\Event',
        'File'          => 'Nova\Support\Facades\File',
        'Forge'         => 'Nova\Support\Facades\Forge',
        'Hash'          => 'Nova\Support\Facades\Hash',
        'Input'         => 'Nova\Support\Facades\Input',
        'Language'      => 'Nova\Support\Facades\Language',
        'Mailer'        => 'Nova\Support\Facades\Mailer',
        'Paginator'     => 'Nova\Support\Facades\Paginator',
        'Password'      => 'Nova\Support\Facades\Password',
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
        'Form'          => 'Nova\Support\Facades\Form',
        'HTML'          => 'Nova\Support\Facades\HTML',
        'Layout'        => 'Nova\Support\Facades\Layout',
        'View'          => 'Nova\Support\Facades\View',
        'Cron'          => 'Nova\Support\Facades\Cron',
        'Module'        => 'Nova\Support\Facades\Module',
    ),
);
