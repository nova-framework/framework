<?php
/**
 * Application Configuration
 *
 * @author David Carr - dave@daveismyname.com
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Nova\Config\Config;


/**
 * The Application configuration.
 */
Config::set('app', array(
    /**
     * Debug Mode
     */
    'debug' => (ENVIRONMENT == 'development'), // When enabled the actual PHP errors will be shown.

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
     * The default Template.
     */
    'template' => 'Default',

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
     * This tool can be used to generate key - http://jeffreybarke.net/tools/codeigniter-encryption-key-generator
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
        'Nova\Template\TemplateServiceProvider',
        'Nova\Cron\CronServiceProvider',
    ),

    /**
     * The Service Providers Manifest path.
     */
    'manifest' => APPDIR .'Boot' .DS .'Cache',

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
        'RainCaptcha'   => 'Nova\Helpers\RainCaptcha',
        'ReservedWords' => 'Nova\Helpers\ReservedWords',
        'SimpleCurl'    => 'Nova\Helpers\SimpleCurl',
        'TableBuilder'  => 'Nova\Helpers\TableBuilder',
        'Tags'          => 'Nova\Helpers\Tags',

        // The Forensics Console.
        'Console'       => 'Nova\Forensics\Console',

        // The Support Classes.
        'Arr'           => 'Nova\Support\Arr',
        'Str'           => 'Nova\Support\Str',

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
        'Session'       => 'Nova\Support\Facades\Session',
        'Validator'     => 'Nova\Support\Facades\Validator',
        'Log'           => 'Nova\Support\Facades\Log',
        'URL'           => 'Nova\Support\Facades\URL',
        'Form'          => 'Nova\Support\Facades\Form',
        'HTML'          => 'Nova\Support\Facades\HTML',
        'Template'      => 'Nova\Support\Facades\Template',
        'View'          => 'Nova\Support\Facades\View',
        'Cron'          => 'Nova\Support\Facades\Cron',
        'Module'        => 'Nova\Support\Facades\Module',
    ),
));
