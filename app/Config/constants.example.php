<?php
/**
 * Framework configuration - the application wide constants
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 15th, 2015
 */

/**
 * Define relative base path.
 */
define('DIR', '/');

/**
 * Set the Application Router.
 */
// Default Routing
define('APPROUTER', '\Nova\Net\Router');
// Classic Routing
//define('APPROUTER', '\App\Core\ClassicRouter');

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
define('DB_PREFIX', 'smvc_');

/**
 * Set prefix for sessions.
 */
define('SESSION_PREFIX', 'smvc_');

/**
 * Optional create a constant for the name of the site.
 */
define('SITE_TITLE', 'V3.0');

/**
 * Optional set a site email address.
 */
// define('SITE_EMAIL', 'email@domain.com');
