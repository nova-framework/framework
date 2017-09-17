<?php

/**
 * Setup the Storage Path.
 */
define('STORAGE_PATH', BASEPATH .'storage' .DS);

/**
 * PREFER to be used in Database calls or storing Session data, default is 'nova_'
 */
define('PREFIX', 'nova_');

/**
 * Setup the Config API Mode.
 *
 * For using the 'database' mode, you need a database having the table 'nova_options'
 */
define('CONFIG_STORE', 'database'); // Supported: "files", "database"
