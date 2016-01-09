<?php
/**
 * Packages Manager - class responsible to Packages management.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 9th, 2016
 */

namespace Nova\Packages;

use Nova\Config;


class Manager
{
    /**
     * Bootstrap the active Modules.
     *
     */
    public static function bootstrap()
    {
        $packages = Config::get('packages');

        if(! $packages) {
            return;
        }

        foreach($packages as $package) {
            $filePath = str_replace('/', DS, APPPATH.'Packages/'.$package.'/Config/bootstrap.php');

            if(!is_readable($filePath)) {
                continue;
            }

            require $filePath;
        }
    }
}
