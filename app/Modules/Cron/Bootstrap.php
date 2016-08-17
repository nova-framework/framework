<?php
/**
 * Bootstrap - the Module's specific Bootstrap.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Foundation\AliasLoader;

// Register the Cron Service Provider to the Application.
App::register('App\Modules\Cron\Providers\CronServiceProvider');

// Register the Cron Facade to the AliasLoader.
$aliasLoader = AliasLoader::getInstance();

$aliasLoader->alias('Cron', 'App\Modules\Cron\Facades\Cron');

// Register a CRON Adapter for testing.
Cron::register('test', 'App\Modules\Cron\Adapters\Test');
