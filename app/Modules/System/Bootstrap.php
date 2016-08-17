<?php
/**
 * Bootstrap - the Module's specific Bootstrap.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

// Register a CRON Adapter for testing.
Cron::register('test', 'App\Modules\System\Cron\Adapters\Test');
