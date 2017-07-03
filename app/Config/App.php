<?php
/**
 * Application Configuration.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */


return array(

	/*
	|--------------------------------------------------------------------------
	| Application Debug Mode
	|--------------------------------------------------------------------------
	|
	| When your application is in debug mode, detailed error messages with
	| stack traces will be shown on every error that occurs within your
	| application. If disabled, a simple generic error page is shown.
	|
	*/

	'debug' => true,

	/*
	|--------------------------------------------------------------------------
	| Base Site URL
	|--------------------------------------------------------------------------
	|
	| URL to your Nova root. Typically this will be your base URL,
	| WITH a trailing slash:
	|
	|   http://example.com/
	|
	| WARNING: You MUST set this value!
	|
	*/

	'url' => 'http://www.novaframework.dev/',

   /*
	|--------------------------------------------------------------------------
	| The Administrator's E-mail Address
	|--------------------------------------------------------------------------
	|
	| The e-mail address for your application's administrator.
	|
	*/

	'email' => 'admin@novaframework.dev',

	/*
	|--------------------------------------------------------------------------
	| The Website Path
	|--------------------------------------------------------------------------
	|
	*/

	'path' => '/',

	/*
	|--------------------------------------------------------------------------
	| Application Name
	|--------------------------------------------------------------------------
	|
	| This value is the name of your application. This value is used when the
	| framework needs to place the application's name in a notification or
	| any other location as required by the application.
	|
	*/

	'name' => 'Nova 4.0',

	/*
	|--------------------------------------------------------------------------
	| Default Theme
	|--------------------------------------------------------------------------
	|
	| Used for the applications default theme.
	|
	*/

	'theme' => 'Bootstrap',

	/*
	|--------------------------------------------------------------------------
	| Application Backend Colour Scheme
	|--------------------------------------------------------------------------
	|
	| Used for the applications Backend AdminLTE template.
	|
	| Options:
	| - blue
	| - blue-light
	| - black
	| - black-light
	| - purple
	| - purple-light
	| - yellow
	| - yellow-light
	| - red
	| - red-light
	| - green
	| - green-light
	|
	*/

	'color_scheme' => 'blue',

	/*
	|--------------------------------------------------------------------------
	| Application Locale Configuration
	|--------------------------------------------------------------------------
	|
	| The application locale determines the default locale that will be used
	| by the translation service provider. You are free to set this value
	| to any of the locales which will be supported by the application.
	|
	*/

	'locale' => 'en',

	/*
	|--------------------------------------------------------------------------
	| Application Timezone
	|--------------------------------------------------------------------------
	|
	| Here you may specify the default timezone for your application, which
	| will be used by the PHP date and date-time functions. We have gone
	| ahead and set this to a sensible default for you out of the box.
	|
	| http://www.php.net/manual/en/timezones.php
	|
	*/

	'timezone' => 'Europe/London',

	/*
	|--------------------------------------------------------------------------
	| Encryption Key
	|--------------------------------------------------------------------------
	|
	| This key is used by the encrypter service and should be set
	| to a random, 32 character string, otherwise these encrypted strings
	| will not be safe. Please do this before deploying an application!
	|
	| This page can be used to generate key - http://novaframework.com/token-generator
	|
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

	/*
	|--------------------------------------------------------------------------
	| Cross Site Request Forgery (CSRF)
	|--------------------------------------------------------------------------
	|
	| Enables a CSRF cookie token to be set. When set to TRUE, token will be
	| checked on a submitted form. If you are accepting user data, it is strongly
	| recommended CSRF protection be enabled.
	|
	*/

	'csrf' => true,

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
		'Nova\Plugins\PluginServiceProvider',
		'Nova\Auth\AuthServiceProvider',
		'Nova\Bus\BusServiceProvider',
		'Nova\Cache\CacheServiceProvider',
		'Nova\Cookie\CookieServiceProvider',
		'Nova\Database\DatabaseServiceProvider',
		'Nova\Encryption\EncryptionServiceProvider',
		'Nova\Filesystem\FilesystemServiceProvider',
		'Nova\Foundation\Providers\FoundationServiceProvider',
		'Nova\Hashing\HashServiceProvider',
		'Nova\Language\LanguageServiceProvider',
		'Nova\Mail\MailServiceProvider',
		'Nova\Pagination\PaginationServiceProvider',
		'Nova\Queue\QueueServiceProvider',
		'Nova\Redis\RedisServiceProvider',
		'Nova\Auth\Reminders\ReminderServiceProvider',
		'Nova\Session\SessionServiceProvider',
		'Nova\Validation\ValidationServiceProvider',
		'Nova\View\ViewServiceProvider',
		'Nova\Broadcasting\BroadcastServiceProvider',

		// The Forge/Console Providers.
		'Nova\Foundation\Providers\ForgeServiceProvider',
		'Nova\Foundation\Providers\ConsoleSupportServiceProvider',
		'Nova\Auth\Reminders\ConsoleServiceProvider',
		'Nova\Cache\ConsoleServiceProvider',
		'Nova\Database\MigrationServiceProvider',
		'Nova\Database\SeedServiceProvider',
		'Nova\Plugins\ConsoleServiceProvider',
		'Nova\Routing\ConsoleServiceProvider',
		'Nova\Session\ConsoleServiceProvider',

		// The Application Providers.
		'App\Providers\AppServiceProvider',
		'App\Providers\AuthServiceProvider',
		'App\Providers\BroadcastServiceProvider',
		'App\Providers\EventServiceProvider',
		'App\Providers\RouteServiceProvider',
	),

	/*
	|--------------------------------------------------------------------------
	| Service Provider Manifest
	|--------------------------------------------------------------------------
	|
	| The service provider manifest is used by Nova to lazy load service
	| providers which are not needed for each request, as well to keep a
	| list of all of the services. Here, you may set its storage spot.
	|
	*/

	'manifest' => STORAGE_PATH,

	/*
	|--------------------------------------------------------------------------
	| Class Aliases
	|--------------------------------------------------------------------------
	|
	| This array of class aliases will be registered when this application
	| is started. However, feel free to register as many as you wish as
	| the aliases are "lazy" loaded so they don't hinder performance.
	|
	*/

	'aliases' => array(
		// The Forensics Console.
		'Console'		=> 'Nova\Forensics\Console',

		// The Support Classes.
		'Arr'			=> 'Nova\Support\Arr',
		'Str'			=> 'Nova\Support\Str',

		// The Database Seeder.
		'Seeder'		=> 'Nova\Database\Seeder',

		// The Support Facades.
		'App'			=> 'Nova\Support\Facades\App',
		'Assets'		=> 'Nova\Support\Facades\Assets',
		'Forge'			=> 'Nova\Support\Facades\Forge',
		'Auth'			=> 'Nova\Support\Facades\Auth',
		'Broadcast'		=> 'Nova\Support\Facades\Broadcast',
		'Bus'			=> 'Nova\Support\Facades\Bus',
		'Cache'			=> 'Nova\Support\Facades\Cache',
		'Config'		=> 'Nova\Support\Facades\Config',
		'Cookie'		=> 'Nova\Support\Facades\Cookie',
		'Crypt'			=> 'Nova\Support\Facades\Crypt',
		'DB'			=> 'Nova\Support\Facades\DB',
		'Event'			=> 'Nova\Support\Facades\Event',
		'File'			=> 'Nova\Support\Facades\File',
		'Gate'			=> 'Nova\Support\Facades\Gate',
		'Hash'			=> 'Nova\Support\Facades\Hash',
		'Input'			=> 'Nova\Support\Facades\Input',
		'Language'		=> 'Nova\Support\Facades\Language',
		'Mail'			=> 'Nova\Support\Facades\Mail',
		'Paginator'		=> 'Nova\Support\Facades\Paginator',
		'Password'		=> 'Nova\Support\Facades\Password',
		'Queue'			=> 'Nova\Support\Facades\Queue',
		'Redirect'		=> 'Nova\Support\Facades\Redirect',
		'Redis'			=> 'Nova\Support\Facades\Redis',
		'Request'		=> 'Nova\Support\Facades\Request',
		'Response'		=> 'Nova\Support\Facades\Response',
		'Route'			=> 'Nova\Support\Facades\Route',
		'Schema'		=> 'Nova\Support\Facades\Schema',
		'Session'		=> 'Nova\Support\Facades\Session',
		'Validator'		=> 'Nova\Support\Facades\Validator',
		'Log'			=> 'Nova\Support\Facades\Log',
		'URL'			=> 'Nova\Support\Facades\URL',
		'View'			=> 'Nova\Support\Facades\View',
		'Widget'		=> 'Nova\Support\Facades\Widget',
		'Template'		=> 'Nova\Support\Facades\Template',
		'Plugin'		=> 'Nova\Support\Facades\Plugin',
	),
);
