<?php
/**
 * Config - the Module's specific Configuration.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Core\Config;

/**
 * Configuration constants and options.
 */

Config::set('cron', array(
    /**
     * The CRON Access Token.
     * This tool can be used to generate token - http://jeffreybarke.net/tools/codeigniter-encryption-key-generator
     */
    'token' => 'SomeRandomStringThere_1234567890'
));


// Register a CRON Adapter for testing.
use App\Modules\Cron\Core\Manager as Cron;

Cron::register('test', 'App\Modules\Cron\Adapters\Test');
