<?php
/**
 * Application Configuration
 *
 * @author David Carr - dave@daveismyname.com
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Core\Config;


/**
 * The Application configuration.
 */
Config::set('app', array(
    /**
     * Debug Mode
     */
    'debug' => true, // When enabled the actual PHP errors will be shown.

    /**
     * The Website URL.
     */
    'url'  => SITEURL,

    /**
     * Website Name.
     */
    'name' => SITETITLE,

    /**
     * Enable multilingual support.
     */
    'multilingual' => false,

    /**
     * The Backend's Color Scheme.
     */
    'color_scheme' => 'blue',

    /**
     * The default locale that will be used by the translation.
     */
    'locale' => LANGUAGE_CODE,

    /**
     * The default Timezone for your website.
     * http://www.php.net/manual/en/timezones.php
     */
    'timezone' => 'UTC',

    /**
     * The Encryption Key.
     */
    'key' => ENCRYPT_KEY,

    /**
     *  Prevents the website from CSRF attacks.
     */
    'csrf' => true,

    /**
     * The registered Service Providers.
     */
    'providers' => array(
        'Auth\AuthServiceProvider',
        'Cache\CacheServiceProvider',
        'Routing\RoutingServiceProvider',
        'Cookie\CookieServiceProvider',
        'Database\DatabaseServiceProvider',
        'Encryption\EncryptionServiceProvider',
        'Hashing\HashServiceProvider',
        'Log\LogServiceProvider',
        'Mail\MailServiceProvider',
        'Pagination\PaginationServiceProvider',
        'Auth\Reminders\ReminderServiceProvider',
        'Session\SessionServiceProvider',
        'Validation\ValidationServiceProvider',
    ),

    /**
     * The Service Providers Manifest path.
     */
    'manifest' => STORAGE_PATH,

    /**
     * The registered Class Aliases.
     */
    'aliases' => array(
        // The Core Tools.
        'Errors'        => '\Core\Error',

        // The Helpers.
        'Mail'          => '\Helpers\Mailer',
        'Assets'        => '\Helpers\Assets',
        'Csrf'          => '\Helpers\Csrf',
        'Date'          => '\Helpers\Date',
        'Document'      => '\Helpers\Document',
        'Encrypter'     => '\Helpers\Encrypter',
        'FastCache'     => '\Helpers\FastCache',
        'Form'          => '\Helpers\Form',
        'Ftp'           => '\Helpers\Ftp',
        'GeoCode'       => '\Helpers\GeoCode',
        'Hooks'         => '\Helpers\Hooks',
        'Inflector'     => '\Helpers\Inflector',
        'Number'        => '\Helpers\Number',
        'RainCaptcha'   => '\Helpers\RainCaptcha',
        'ReservedWords' => '\Helpers\ReservedWords',
        'SimpleCurl'    => '\Helpers\SimpleCurl',
        'TableBuilder'  => '\Helpers\TableBuilder',
        'Tags'          => '\Helpers\Tags',
        'Url'           => '\Helpers\Url',

        // The Forensics Console.
        'Console'       => '\Forensics\Console',

        // The Support Classes.
        'Arr'           => '\Support\Arr',
        'Str'           => '\Support\Str',

        // The Support Facades.
        'App'           => '\Support\Facades\App',
        'Auth'          => '\Support\Facades\Auth',
        'Cache'         => '\Support\Facades\Cache',
        'Config'        => '\Support\Facades\Config',
        'Cookie'        => '\Support\Facades\Cookie',
        'Crypt'         => '\Support\Facades\Crypt',
        'DB'            => '\Support\Facades\DB',
        'Event'         => '\Support\Facades\Event',
        'Hash'          => '\Support\Facades\Hash',
        'Input'         => '\Support\Facades\Input',
        'Language'      => '\Support\Facades\Language',
        'Mailer'        => '\Support\Facades\Mailer',
        'Paginator'     => '\Support\Facades\Paginator',
        'Password'      => '\Support\Facades\Password',
        'Redirect'      => '\Support\Facades\Redirect',
        'Request'       => '\Support\Facades\Request',
        'Response'      => '\Support\Facades\Response',
        'Session'       => '\Support\Facades\Session',
        'Validator'     => '\Support\Facades\Validator',
        'Log'           => '\Support\Facades\Log',
    ),
));
