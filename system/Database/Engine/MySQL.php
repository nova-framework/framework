<?php
/**
 * MySQL Engine.
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


class MySQL extends BaseEngine
{

    /**
     * MySQLEngine constructor.
     * Please use the Factory to maintain instances of the drivers.
     *
     * @param $config array
     *
     * @throws \PDOException
     */
    public function __construct($config) {
        // Check for valid Config.
        if (! is_array($config)) {
            throw new \UnexpectedValueException('Parameter should be an Array');
        }

        // Default port if no port is provided.
        if (! isset($config['port'])) {
            $config['port'] = 3306;
        }

        // Some Database Servers go crazy when a charset parameter is added, then we should make it optional.
        if (! isset($config['charset'])) {
            $charsetStr = "";
        }
        else {
            $charsetStr = ($config['charset'] == 'auto') ? "" : ";charset=" . $config['charset'];
        }

        // Prepare the PDO's options.
        if (isset($config['compress']) && ($config['compress'] === true)) {
            $options = array(\PDO::MYSQL_ATTR_COMPRESS => true);
        }
        else {
            $options = array();
        }

        // Prepare the PDO's DSN
        $dsn = "mysql:host=" .$config['host'] .";port=" .$config['port'] .";dbname=" .$config['database'] .$charsetStr;

        parent::__construct($dsn, $config, $options);
    }

    /**
     * Get the name of the driver
     * @return string
     */
    public function getDriverName()
    {
        return __d('system', 'MySQL Driver');
    }

    /**
     * Get driver code, used in config as driver string.
     * @return string
     */
    public function getDriverCode()
    {
        return Manager::DRIVER_MYSQL;
    }

    /**
     * Truncate table
     * @param  string $table table name
     * @return int number of rows affected
     */
    public function truncate($table)
    {
        $this->queryCount++;

        return $this->exec("TRUNCATE TABLE $table");
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
        $result = $this->rawQuery("SHOW COLUMNS FROM $table", 'array');

        if($result !== false) {
            foreach ($result as $row) {
                // Get the column name from the results
                $columns[] = $row['Field'];
            }
        }

        return $columns;
    }
}
