<?php
/**
 * Boot Handler - perform the Application's boot stage.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Nova\Config\EnvironmentVariables;
use Nova\Config\Config;
use Nova\Config\Repository as ConfigRepository;
use Nova\Foundation\AliasLoader;
use Nova\Foundation\Application;
use Nova\Http\Request;
use Nova\Support\Facades\Facade;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

//--------------------------------------------------------------------------
// Set PHP Error Reporting Options
//--------------------------------------------------------------------------

error_reporting(-1);

//--------------------------------------------------------------------------
// Use Internally The UTF-8 Encoding
//--------------------------------------------------------------------------

if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('utf-8');
}

//--------------------------------------------------------------------------
// Include The Compiled Class File
//--------------------------------------------------------------------------

if (file_exists($compiled = __DIR__ .DS .'Compiled.php')) {
    require $compiled;
}

//--------------------------------------------------------------------------
// Setup Patchwork UTF-8 Handling
//--------------------------------------------------------------------------

Patchwork\Utf8\Bootup::initMbstring();

//--------------------------------------------------------------------------
// Set The System Path
//--------------------------------------------------------------------------

define('SYSPATH', BASEPATH .str_replace('/', DS, 'vendor/nova-framework/system/'));

//--------------------------------------------------------------------------
// Set The Storage Path
//--------------------------------------------------------------------------

defined('STORAGE_PATH') || define('STORAGE_PATH', BASEPATH .'storage' .DS);

//--------------------------------------------------------------------------
// Set The Framework Version
//--------------------------------------------------------------------------

define('VERSION', Application::VERSION);

//--------------------------------------------------------------------------
// Load The Global Configuration
//--------------------------------------------------------------------------

$path = APPPATH .'Config.php';

if (is_readable($path)) require $path;

//--------------------------------------------------------------------------
// Create New Application
//--------------------------------------------------------------------------

$app = new Application();

//--------------------------------------------------------------------------
// Detect The Application Environment
//--------------------------------------------------------------------------

$env = $app->detectEnvironment(array(
    'local' => array('darkstar'),
));

//--------------------------------------------------------------------------
// Bind Paths
//--------------------------------------------------------------------------

$app->bindInstallPaths(array(
    'base'    => BASEPATH,
    'app'     => APPPATH,
    'public'  => WEBPATH,
    'storage' => STORAGE_PATH,
));

//--------------------------------------------------------------------------
// Bind The Application In The Container
//--------------------------------------------------------------------------

$app->instance('app', $app);

//--------------------------------------------------------------------------
// Check For The Test Environment
//--------------------------------------------------------------------------

if (isset($unitTesting)) {
    $app['env'] = $env = $testEnvironment;
}

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
// Register The Environment Variables
//--------------------------------------------------------------------------

with($envVariables = new EnvironmentVariables(
    $app->getEnvironmentVariablesLoader()
))->load($env);

//--------------------------------------------------------------------------
// Register The Config Manager
//--------------------------------------------------------------------------

$app->instance('config', $config = new ConfigRepository(
    $app->getConfigLoader(), $env
));

//--------------------------------------------------------------------------
// Register Application Exception Handling
//--------------------------------------------------------------------------

$app->startExceptionHandling();

if ($env != 'testing') ini_set('display_errors', 'Off');

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

App::middleware('App\Http\Middleware\ContentGuard', array(
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

$path = $app['path'] .DS .'Boot' .DS .'Global.php';

if (is_readable($path)) require $path;

//--------------------------------------------------------------------------
// Load The Environment Start Script
//--------------------------------------------------------------------------

$path = $app['path'] .DS .'Boot' .DS .'Environment' .DS .ucfirst($env) .'.php';

if (is_readable($path)) require $path;

});

//--------------------------------------------------------------------------
// Return The Application
//--------------------------------------------------------------------------

return $app;
