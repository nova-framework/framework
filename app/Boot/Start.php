<?php
/**
 * Boot Handler - perform the Application's boot stage.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

//--------------------------------------------------------------------------
// Load The Composer Autoloader
//--------------------------------------------------------------------------

require ROOTDIR .'vendor/autoload.php';

// The used Classes.
use Nova\Config\Config;
use Nova\Config\Repository as ConfigRepository;
use Nova\Foundation\AliasLoader;
use Nova\Foundation\Application;
use Nova\Http\Request;
use Nova\Http\RequestProcessor;
use Nova\Support\Facades\Facade;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

//--------------------------------------------------------------------------
// Set PHP Error Reporting Options
//--------------------------------------------------------------------------

error_reporting(-1);

//--------------------------------------------------------------------------
// Set PHP Session Cache Limiter
//--------------------------------------------------------------------------

session_cache_limiter('');

//--------------------------------------------------------------------------
// Use Internally The UTF-8 Encoding
//--------------------------------------------------------------------------

if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('utf-8');
}

//--------------------------------------------------------------------------
// Setup Patchwork UTF-8 Handling
//--------------------------------------------------------------------------

Patchwork\Utf8\Bootup::initMbstring();

//--------------------------------------------------------------------------
// Set The Storage Path
//--------------------------------------------------------------------------

define('STORAGE_PATH', APPDIR .'Storage' .DS);

//--------------------------------------------------------------------------
// Set The Framework Version
//--------------------------------------------------------------------------

define('VERSION', Application::VERSION);

//--------------------------------------------------------------------------
// Load Global Configuration
//--------------------------------------------------------------------------

$path = APPDIR .'Config.php';

if (is_readable($path)) require $path;

//--------------------------------------------------------------------------
// Create New Application
//--------------------------------------------------------------------------

$app = new Application();

$app->instance('app', $app);

//--------------------------------------------------------------------------
// Detect The Application Environment
//--------------------------------------------------------------------------

$env = $app->detectEnvironment(array(
    'local' => array('darkstar'),
));

//--------------------------------------------------------------------------
// Bind Paths
//--------------------------------------------------------------------------

$paths = array(
    'base'    => ROOTDIR,
    'app'     => APPDIR,
    'public'  => PUBLICDIR,
    'storage' => STORAGE_PATH,
);

$app->bindInstallPaths($paths);

//--------------------------------------------------------------------------
// Load The Framework Facades
//--------------------------------------------------------------------------

Facade::clearResolvedInstances();

Facade::setFacadeApplication($app);

//--------------------------------------------------------------------------
// Register Facade Aliases To Full Classes
//--------------------------------------------------------------------------

$app->registerCoreContainerAliases();

//--------------------------------------------------------------------------
// Register Application Exception Handling
//--------------------------------------------------------------------------

$app->startExceptionHandling();

if ($env != 'testing') ini_set('display_errors', 'Off');

//--------------------------------------------------------------------------
// Load The Configuration
//--------------------------------------------------------------------------

foreach (glob(app_path() .DS .'Config/*.php') as $path) {
    if (is_readable($path)) require $path;
}

//--------------------------------------------------------------------------
// Register The Config Manager
//--------------------------------------------------------------------------

$app->instance('config', $config = new ConfigRepository(
    $app->getConfigLoader()
));

//--------------------------------------------------------------------------
// Set The Default Timezone From Configuration
//--------------------------------------------------------------------------

$config = $app['config']['app'];

date_default_timezone_set($config['timezone']);

//--------------------------------------------------------------------------
// Register The Alias Loader
//--------------------------------------------------------------------------

$aliases = $config['aliases'];

AliasLoader::getInstance($aliases)->register();

//--------------------------------------------------------------------------
// Enable HTTP Method Override
//--------------------------------------------------------------------------

Request::enableHttpMethodParameterOverride();

//--------------------------------------------------------------------------
// Enable Trusting Of X-Sendfile Type Header
//--------------------------------------------------------------------------

BinaryFileResponse::trustXSendfileTypeHeader();

//--------------------------------------------------------------------------
// Register The Core Service Providers
//--------------------------------------------------------------------------

$providers = $config['providers'];

$app->getProviderRepository()->load($app, $providers);

//--------------------------------------------------------------------------
// Additional Middleware On Application
//--------------------------------------------------------------------------

App::middleware('Shared\Http\ContentGuard', array(
    $app['config']['app.debug']
));

//--------------------------------------------------------------------------
// Register Booted Start Files
//--------------------------------------------------------------------------

$app->booted(function() use ($app, $env)
{

//--------------------------------------------------------------------------
// Load The Application Start Script
//--------------------------------------------------------------------------

$path = app_path() .DS .'Boot' .DS .'Global.php';

if (is_readable($path)) require $path;

//--------------------------------------------------------------------------
// Load The Environment Start Script
//--------------------------------------------------------------------------

$path = app_path() .DS .'Boot' .DS .'Environment' .DS .ucfirst($env) .'.php';

if (is_readable($path)) require $path;

//--------------------------------------------------------------------------
// Load The Application Events
//--------------------------------------------------------------------------

$path = app_path() .DS .'Events.php';

if (is_readable($path)) require $path;

//--------------------------------------------------------------------------
// Load The Application's Route Filters
//--------------------------------------------------------------------------

$path = app_path() .DS .'Filters.php';

if (is_readable($path)) require $path;

//--------------------------------------------------------------------------
// Load The Application Routes
//--------------------------------------------------------------------------

$path = app_path() .DS .'Routes.php';

if (is_readable($path)) require $path;

//--------------------------------------------------------------------------
// Load The Application Bootstrap
//--------------------------------------------------------------------------

$path = app_path() .DS .'Bootstrap.php';

if (is_readable($path)) require $path;

});

//--------------------------------------------------------------------------
// Execute The Application
//--------------------------------------------------------------------------

$app->run();
