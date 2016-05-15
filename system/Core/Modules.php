<?php
/**
 * Modules - A Class responsible for the Modules initialization.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Core;

use Core\Config;


class Modules
{
    /**
     * The loaded Modules configuration.
     *
     * @var array
     */
    static $modules = array();

    /**
     * Load the Configuration and the Route Filters from the active Modules.
     */
    public static function init()
    {
        // Load the the Modules configuration.
        static::$modules = Config::get('modules');

        // Load the Config(uration) and Routes Filters from every specified Module.
        foreach (static::$modules as $module) {
            foreach (array('Config', 'Filters') as $file) {
                $filePath = APPDIR .'Modules' .DS .$module .DS .$file .'.php';

                if (! is_readable($filePath)) {
                    continue;
                }

                require $filePath;
            }
        }
    }

    /**
     * Load the Routes from the active Modules.
     */
    public static function loadRoutes()
    {
        // Load the Routes from every specified Module.
        foreach (static::$modules as $module) {
            $filePath = APPDIR .'Modules' .DS .$module .DS .'Routes.php';

            if (! is_readable($filePath)) {
                continue;
            }

            require $filePath;
        }
    }
}
