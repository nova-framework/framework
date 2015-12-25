<?php
/**
 * Routes - all Routes for the current Module are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 23th, 2015
 */

use Nova\Net\Router;


/** The Module's known Routes definition. */
Router::get('demos(/index)', 'App\Modules\Demo\Controllers\Index@home');

/** Controller Demo */
Router::any('demos/themed/welcome', 'App\Modules\Demo\Controllers\Themed@welcome');
Router::any('demos/themed/subpage', 'App\Modules\Demo\Controllers\Themed@subPage');
Router::any('demos/classic/welcome', 'App\Modules\Demo\Controllers\Classic@welcome');
Router::any('demos/classic/subpage', 'App\Modules\Demo\Controllers\Classic@subPage');

/** Event & Event Listener demo */
Router::any('demos/events', 'App\Modules\Demo\Controllers\Events@index');

/** Database & Database Abstraction Layer demo */
Router::get('demos/database/mysqlexport', 'App\Modules\Demo\Controllers\Database\Export@mysql');
Router::get('demos/database/engine/basic/mysql', 'App\Modules\Demo\Controllers\Database\Engine@basicMysql');
Router::get('demos/database/engine/basic/sqlite', 'App\Modules\Demo\Controllers\Database\Engine@basicSqlite');
Router::get('demos/database/service/basic/mysql', 'App\Modules\Demo\Controllers\Database\Service@basicMysql');
Router::get('demos/database/service/basic/sqlite', 'App\Modules\Demo\Controllers\Database\Service@basicSqlite');
