<?php
/**
 * Config - an example for setting up system settings.
 * When you are done editing, rename this file to 'Config.php'.
 *
 * @author David Carr - dave@daveismyname.com
 * @author Edwin Hoksberg - info@edwinhoksberg.nl
 * @version 3.0
 */

use Core\Config;

/**
 * Define the complete site URL.
 */
define('SITEURL', 'http://novaframework.dev/');

/**
 * Define relative base path.
 */
define('DIR', '/');

/**
 * Set the Application Router.
 */
// Default Routing
define('APPROUTER', '\Core\Router');
// Classic Routing
// define('APPROUTER', '\Core\ClassicRouter');

/**
 * Set timezone.
 */
define('DEFAULT_TIMEZONE', 'Europe/London');

/**
 * Set default controller and method for legacy calls.
 */
define('DEFAULT_CONTROLLER', 'Welcome');
define('DEFAULT_METHOD', 'index');

/**
 * Set the default template.
 */
define('TEMPLATE', 'Default');

/**
 * Set a default language.
 */
define('LANGUAGE_CODE', 'en');

/**
 * Set a prefix for sessions.
 */
define('SESSION_PREFIX', 'nova_');

/**
 * OPTIONAL, create a constant for the name of the site.
 */
define('SITETITLE', 'Nova V3.0');

/**
 * OPTIONAL, set a site email address.
 */
// define('SITEEMAIL', 'email@domain.com');

/**
 * Define a 32 bit Encryption Key.
 */
define('ENCRYPT_KEY', '');

/**
 * Setup the Language configuration
 */
require 'Configs/Language.php';

/**
 * Setup the Module cnfiguration
 */
require 'Configs/Module.php';

/**
 * Setup the Database configuration
 */
require 'Configs/ClassAliases.php';

/**
 * Setup the Class Aliases configuration
 */
require 'Configs/Database.php';

/**
 * Setup the Auth configuration.
 */
require 'Configs/Auth.php';

/**
 * Setup the FastCache configuration.
 */
require 'Configs/Cache.php';