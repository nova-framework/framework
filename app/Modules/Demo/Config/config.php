<?php
/**
 * Module configuration - the configuration parameters of the Framework's Module.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 23th, 2015
 */

use Nova\Config;
use Nova\Events\Manager as Events;

/**
 * Additional Configuration for this Module.
 */
Config::set('demo_message', 'Shiny Demo!');


/*
 * The Administration Menu
 */
Config::set('demos_menu', array(
    'dashboard' => array(
        'name'      => __d('demo', 'Dashboard'),
        'url'       => site_url('demos/index'),
        'icon'      => 'fa fa-dashboard',
    ),
    'platform' => array(
        'name'      => __d('demo', 'Controllers'),
        'icon'      => 'fa fa-server',
        'children'  => array(
            array(
                'name' => __d('demo', 'Themed Controller'),
                'url'  => site_url('demos/themed/welcome'),
                'icon' => 'fa fa-gears',
            ),
            array(
                'name' => __d('demo', 'Classic Controller'),
                'url'  => site_url('demos/classic/welcome'),
                'icon' => 'fa fa-gears',
            ),
        ),
    ),
    'events' => array(
        'name'      => __d('demo', 'Events'),
        'url'       => site_url('demos/events'),
        'icon'      => 'fa fa-gears',
    ),
    'database' => array(
        'name'      => __d('demo', 'Database'),
        'icon'      => 'fa fa-server',
        'children'  => array(
            'dbal' => array(
                'name'      => __d('demo', 'Database Abstraction Layer'),
                'url'       => site_url('demos/dbal'),
                'icon'      => 'fa fa-gears',
            ),
            'query_builder' => array(
                'name'      => __d('demo', 'Database Query Builder'),
                'url'       => site_url('demos/dbal/query_builder'),
                'icon'      => 'fa fa-gears',
            ),
        ),
    ),
    'models' => array(
        'name'      => __d('demo', 'Models'),
        'icon'      => 'fa fa-server',
        'children'  => array(
            'classic_model' => array(
                'name'      => __d('demo', 'Classic BaseModel'),
                'url'       => site_url('demos/models/base_model'),
                'icon'      => 'fa fa-gears',
            ),
            'orm_model' => array(
                'name'      => __d('demo', 'ORM - Relational Model'),
                'url'       => site_url('demos/models/orm_model'),
                'icon'      => 'fa fa-gears',
            ),
        ),
    ),
));

/**
 * Events Management for this Module.
 */

Events::addListener('welcome', 'App\Modules\Demo\Controllers\EventListener@welcome');
