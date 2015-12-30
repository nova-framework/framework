<?php
/**
 * SQLite Engine.
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 27th, 2015
 */

namespace Nova\Database\Engine;

use Nova\Database\Engine;
use Nova\Database\Manager;
use Nova\Database\Engine\Base as BaseEngine;


class SQLite extends BaseEngine
{

    /**
     * SQLiteEngine constructor.
     * Please use the Factory to maintain instances of the drivers.
     *
     * @param $config array
     * @throws \PDOException
     */
    public function __construct($config) {
        // Check for valid Config.
        if (! is_array($config)) {
            throw new \UnexpectedValueException('Parameter should be an Array');
        }

        // Prepare the PDO's DSN
        $dsn = "sqlite:" .BASEPATH .'storage' .DS .'persistent' .DS .$config['file'];

        parent::__construct($dsn, $config);
    }

    /**
     * Get the name of the driver
     * @return string
     */
    public function getDriverName()
    {
        return __d('system', 'SQLite Driver');
    }

    /**
     * Get driver code, used in config as driver string.
     * @return string
     */
    public function getDriverCode()
    {
        return Manager::DRIVER_SQLITE;
    }

    /**
     * Truncate table
     * @param  string $table table name
     * @return int number of rows affected
     */
    public function truncate($table)
    {
        throw new \BadMethodCallException('TRUNCATE called on SQLite Engine');
    }

    /**
     * Get the field names for the specified Database Table.
     *
     * @param  string $table table name
     * @return array  Returns the Database Table fields
     */
    public function listFields($table)
    {
        $columns = array();

        if (empty($table)) {
            throw new \UnexpectedValueException('Parameter should be not empty');
        }

        // Find all Column names
        $result = $this->rawQuery("PRAGMA table_info($table)", 'array');

        if($result !== false) {
            foreach ($result as $row) {
                // Get the column name from the results
                $columns[] = $row['name'];
            }
        }

        return $columns;
    }

}
