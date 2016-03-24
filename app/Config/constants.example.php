<?php
/**
 * Framework configuration - the application wide constants
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 15th, 2015
 */

/**
 * Define the complete site URL.
 */
define('SITE_URL', 'http://www.novaframework.dev/');

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
 * PREFER to be used in database calls default is nova_
 */
define('DB_PREFIX', 'nova_');

/**
 * Set prefix for sessions.
 */
define('SESSION_PREFIX', 'nova_');

/**
 * Optional create a constant for the name of the site.
 */
define('SITE_TITLE', 'NovaIgniter');

/**
 * Optional set a site email address.
 */
// define('SITE_EMAIL', 'email@domain.com');

/**
 * Set the Cache Path.
 */
define('CACHEPATH', BASEPATH .'storage' .DS .'cache');
