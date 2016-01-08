<?php
/**
 * Database Helper
 *
 * @author David Carr - dave@daveismyname.com
 * @version 2.1
 * @date June 27, 2014
 * @date updated Sept 19, 2015
 */

namespace Nova\Helpers;

use Nova\Database\Manager as Database;
use Nova\Database\Connection;

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
     * @param mixed $linkName
     * @return \Nova\Database\Connection | null
     * @deprecated use the \Nova\Database\Manager !
     *
     * @throws \Exception
     */
    public static function get($linkName = false)
    {
        if(is_array($linkName))
            throw new \Exception(__d('system', 'Invalid Configuration on the Legacy Helper');
        }

        // Adjust the linkName value, if case.
        $linkName = $linkName ? $linkName : null;

        // Return the Connection instance from Nova\Database\Manager.
        return Database::getConnection($linkName);
    }

}
