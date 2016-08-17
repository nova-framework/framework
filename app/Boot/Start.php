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
use Core\Config;
use Config\Repository as ConfigRepository;
use Foundation\AliasLoader;
use Foundation\Application;
use Http\Request;
use Http\RequestProcessor;
use Support\Facades\Facade;

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
// Create New Application
//--------------------------------------------------------------------------

$app = new Application();

$app->instance('app', $app);

//--------------------------------------------------------------------------
// Detect The Application Environment
//--------------------------------------------------------------------------

$env = $app->detectEnvironment(array(
    'local'   => array('your-local-machine-name'),
    'testing' => array('darkstar'),
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

// Load first the file constants file.
$path = app_path() .'Config.php';

if (is_readable($path)) require $path;

// Include all other files located on Config directory.
foreach (glob(app_path() .'Config/*.php') as $path) {
    if (is_readable($path)) require $path;
}

// Load the Modules configuration.
$modules = Config::get('modules');

foreach ($modules as $module) {
    $path = app_path() .'Modules' .DS .$module .DS .'Config.php';

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

App::middleware('App\Extensions\Http\ContentGuard', array(
    $app['config']['app.debug']
));

//--------------------------------------------------------------------------
// Register Booted Start Files
//--------------------------------------------------------------------------

$app->booted(function() use ($app, $env, $modules)
{

//--------------------------------------------------------------------------
// Load The Application Start Script
//--------------------------------------------------------------------------

$path = $app['path'] .DS .'Boot' .DS .'Global.php';

if (is_readable($path)) require $path;

//--------------------------------------------------------------------------
// Load The Environment Start Script
//--------------------------------------------------------------------------

$path = $app['path'] .DS .'Boot' .DS .'Environment' .DS .ucfirst($env) .'.php';

if (is_readable($path)) require $path;

//--------------------------------------------------------------------------
// Load The Application Events
//--------------------------------------------------------------------------

// Load the Events defined on Modules.
foreach ($modules as $module) {
    $path = app_path() .'Modules' .DS .$module .DS .'Events.php';

    if (is_readable($path)) require $path;
}

// Load the Events defined on App.
$path = app_path() .'Events.php';

if (is_readable($path)) require $path;

//--------------------------------------------------------------------------
// Load The Application's Route Filters
//--------------------------------------------------------------------------

// Load the Filters defined on Modules.
foreach ($modules as $module) {
    $path = app_path() .'Modules' .DS .$module .DS .'Filters.php';

    if (is_readable($path)) require $path;
}

// Load the Filters defined on App.
$path = app_path() .'Filters.php';

if (is_readable($path)) require $path;

//--------------------------------------------------------------------------
// Load The Application Routes
//--------------------------------------------------------------------------

// Load the Routes defined on Modules.
foreach ($modules as $module) {
    $path = app_path() .'Modules' .DS .$module .DS .'Routes.php';

    if (is_readable($path)) require $path;
}

// Load the Routes defined on App.
$path = app_path() .'Routes.php';

if (is_readable($path)) require $path;

//--------------------------------------------------------------------------
// Load The Modules Bootstrap
//--------------------------------------------------------------------------

// Load the Bootstrap files existing on Modules.
foreach ($modules as $module) {
    $path = app_path() .'Modules' .DS .$module .DS .'Bootstrap.php';

    if (is_readable($path)) require $path;
}

});

//--------------------------------------------------------------------------
// Execute The Application
//--------------------------------------------------------------------------

$app->run();
