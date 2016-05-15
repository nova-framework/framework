<?php
/**
 * Modules Manager - class responsible to Modules management.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Core;

use Core\Config;


class Modules
{
    /**
     * There we load the Modules configuration.
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

        foreach (static::$modules as $module) {
            // Load the Configuration.
            $filePath = str_replace('/', DS, APPDIR.'Modules/'.$module.'/Config.php');

            if (is_readable($filePath)) {
                require $filePath;
            }

            // Load the Route Filters.
            $filePath = str_replace('/', DS, APPDIR.'Modules/'.$module.'/Filters.php');

            if (is_readable($filePath)) {
                require $filePath;
            }
        }
    }

    /**
     * Load the Routes from the active Modules.
     */
    public static function loadRoutes()
    {
        foreach (static::$modules as $module) {
            $filePath = str_replace('/', DS, APPDIR.'Modules/'.$module.'/Routes.php');

            if (is_readable($filePath)) {
                require $filePath;
            }
        }
    }
}
