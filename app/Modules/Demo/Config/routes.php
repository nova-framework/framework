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

/** Backend Demo */
Router::any('demos/backend(/index)', 'App\Modules\Demo\Controllers\Backend@index');

/** DBAL Demo */
Router::any('demos/dbal(/index)', 'App\Modules\Demo\Controllers\Database@index');

/** DBAL's QueryBuilder Demo */
Router::any('demos/dbal/query_builder(/index)', 'App\Modules\Demo\Controllers\Database\QueryBuilder@index');

/** Classic BaseModel Demo */
Router::any('demos/models/base_model(/index)', 'App\Modules\Demo\Controllers\Models\BaseModel@index');

/** ORM - Relational Model Demo */
Router::any('demos/models/orm_model(/index)', 'App\Modules\Demo\Controllers\Models\RelationalModel@index');

/** Event & Event Listener demo */
Router::any('demos/events', 'App\Modules\Demo\Controllers\Events@index');
