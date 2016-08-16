<?php
/**
 * Routes - all Module's specific Routes are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/** Define static routes. */

Route::get('cron/{token}', 'App\Modules\Cron\Controllers\Cron@run');
