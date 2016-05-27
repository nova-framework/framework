<?php
/**
 * Config - an example for setting up system settings.
 * When you are done editing, rename this file to 'Config.php'.
 *
 * @author David Carr - dave@daveismyname.com
 * @author Edwin Hoksberg - info@edwinhoksberg.nl
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Core\Config;

/**
 * Define the complete site URL.
 */
define('SITEURL', 'http://www.novaframework.dev/');

/**
 * Define relative base path.
 */
define('DIR', '/');

/**
 * Set the Application Router.
 */
// Default Routing
define('APPROUTER', '\Routing\Router');
// Classic Routing
// define('APPROUTER', '\Routing\ClassicRouter');

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

//
// database details ONLY NEEDED IF USING A DATABASE

/**
 * Database engine, default is mysql.
 */
define('DB_TYPE', 'mysql');

/**
 * Database host, default is localhost.
 */
define('DB_HOST', 'localhost');

/**
 * Database name.
 */
define('DB_NAME', 'nova');

/**
 * Database username.
 */
define('DB_USER', 'root');

/**
 * Database password.
 */
define('DB_PASS', '');

/**
 * PREFER to be used in database calls, default is nova_
 */
define('PREFIX', 'nova_');

/**
 * Set a prefix for Sessions.
 */
define('SESSION_PREFIX', 'nova_');

/**
 * Set where the Framework store the Session files.
 */
define('SESSION_PATH', APPDIR .'Storage' .DS .'Sessions');

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
 * This tool can be used to generate key - http://jeffreybarke.net/tools/codeigniter-encryption-key-generator
 */
define('ENCRYPT_KEY', '');

/**
 * Set the Cache files Path.
 */
define('CACHEPATH', APPDIR .'Storage' .DS .'Cache');

/**
 * Setup the Session configuration
 */
require 'Config/Session.php';

/**
 * Setup the Language configuration
 */
require 'Config/Languages.php';

/**
 * Setup the Module cnfiguration
 */
require 'Config/Modules.php';

/**
 * Setup the Database configuration
 */
require 'Config/ClassAliases.php';

/**
 * Setup the Class Aliases configuration
 */
require 'Config/Database.php';

/**
 * Setup the Auth configuration.
 */
require 'Config/Auth.php';

/**
 * Setup the Mailing API configuration.
 */
require 'Config/Mail.php';

/**
 * Setup the FastCache configuration.
 */
require 'Config/Cache.php';
