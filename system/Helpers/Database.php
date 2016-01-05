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

use Nova\DBAL\Manager as Database;
use Nova\DBAL\Connection;

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
        if($group !== false)
            throw new \Exception("Trying to select the DBAL Configuration from Legacy Database");
        }

        // Return the Default Connection from DBAL.
        return Database::getConnection();
    }
}
