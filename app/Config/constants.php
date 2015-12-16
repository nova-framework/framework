<?php
/**
 * Framework configuration - the application wide constants
 *
 * @author Virgil-Adrian Teaca - virgil@@giulianaeassociati.com
 * @version 3.0
 * @date December 15th, 2015
 */

/**
 * Define relative base path.
 */
define('URI_PREFIX', '/');

/**
 * Set the Application Router.
 */
// Default Routing
define('APPROUTER', '\Smvc\Net\Router');
// Classic Routing
//define('APPROUTER', '\App\Core\ClassicRouter');

/**
 * Set default controller and method for legacy calls.
 */
define('DEFAULT_CONTROLLER', 'Welcome');
define('DEFAULT_METHOD', 'index');

/**
 * Set the default template.
 */
define('TEMPLATE', 'default');

/**
 * Set a default language.
 */
define('LANGUAGE_CODE', 'en');

/**
 * PREFER to be used in database calls default is smvc_
 */
define('PREFIX', 'smvc_');

/**
 * Set prefix for sessions.
 */
define('SESSION_PREFIX', 'smvc_');

/**
 * Optional create a constant for the name of the site.
 */
define('SITETITLE', 'V3.0');

/**
 * Optional set a site email address.
 */
// define('SITEEMAIL', 'email@domain.com');
