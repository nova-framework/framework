<?php
/**
 * Auth configuration
 *
 * @author David Carr - dave@daveismyname.com
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Core\Config;

Config::set('authentication', array(
    //'model'      => 'Auth\Model',
    'model'      => 'App\Modules\Users\Models\Users',
    // The used Table name and its primary key.
    'table'      => 'users',
    'primaryKey' => 'id',
    // The used Table columns.
    'columns' => array(
        'password'      => 'password',
        'rememberToken' => 'remember_token'
    ),
));