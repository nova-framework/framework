<?php
/**
 * database Helper
 *
 * @author David Carr - dave@daveismyname.com
 * @version 2.1
 * @date June 27, 2014
 * @date updated Sept 19, 2015
 */

namespace Nova\Helpers;

use Nova\Database\Engine\MySQL;
use Nova\Database\Manager;

/**
 * Extending PDO to use custom methods.
 *
 * @deprecated since v3.0
 */
class Database
{
    /**
     * Static method get
     *
     * @param mixed $group
     * @return MySQL|null
     * @deprecated use the engine factory!
     * @throws \Exception
     */
    public static function get($group = false)
    {
        // Make the engine with the factory
        $engine = Manager::getEngine();

        if ($engine instanceof MySQL) {
            return $engine;
        }

        throw new \Exception("Default config database is not MySQL! Use the new engines.");
    }
}
