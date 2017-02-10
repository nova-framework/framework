<?php
/**
 * Config - the Plugin's specific Configuration.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */


return array(
    // The path where database dumps are stored.
    'path'  => APPPATH .'Database' .DS .'Backup',

    // The paths to the MySQL tools used by Forge.
    'mysql' => array(
        'dumpCommandPath'    => '/usr/bin/mysqldump',
        'restoreCommandPath' => '/usr/bin/mysql',
    ),

    
    // Wheter or not the dump file is compressed.
    'compress' => true,
);
