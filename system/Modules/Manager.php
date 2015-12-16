<?php
/**
 * Modules Manager - class responsible to Modules management.
 *
 * @author Virgil-Adrian Teaca - virgil@@giulianaeassociati.com
 * @version 3.0
 * @date December 16th, 2015
 */

namespace Smvc\Modules;

use Smvc\Config;


class Manager
{
    /**
     * Bootstrap the active Modules.
     *
     */
    public static function bootstrap()
    {
        $modules = Config::get('modules');

        if(! $modules) {
            return;
        }

        foreach($modules as $module) {
            $filePath = str_replace('/', DS, APP.'Modules/'.$module.'/Config/bootstrap.php');

            if(!is_readable($filePath)) {
                continue;
            }

            require $filePath;
        }
    }
}
