<?php

/**
 * Config - the Global Configuration loaded BEFORE the Nova Application starts.
 */


/**
 * Define the path to Storage.
 *
 * NOTE: in a multi-tenant design, every application should have its unique Storage.
 */
define('STORAGE_PATH', BASEPATH .'storage' .DS);

/**
 * Define the global prefix.
 *
 * PREFER to be used in Database calls or storing Session data, default is 'nova_'
 */
define('PREFIX', 'nova_');

/**
 * Setup the Config API Mode.
 *
 * For using the 'database' mode, you need a database having the table 'nova_options'
 */
define('CONFIG_STORE', 'database'); // Supported: "files", "database"
