<?php
/**
 * SQLite Engine.
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 27th, 2015
 */

namespace Nova\Database\Driver;

use Nova\Database\Connection;
use Nova\Database\Manager;

class SQLite extends Connection
{

    /**
     * SQLiteEngine constructor.
     * Please use the Factory to maintain instances of the drivers.
     *
     * @param $config array
     * @throws \PDOException
     */
    public function __construct($config)
    {
        // Check for valid Config.
        if (! is_array($config)) {
            throw new \UnexpectedValueException('Parameter should be an Array');
        }

        // Prepare the PDO's DSN
        $dsn = "sqlite:" .$config['path'];

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
    public function listColumns($table)
    {
        $columns = array();

        if (empty($table)) {
            throw new \UnexpectedValueException('Parameter should be not empty');
        }

        if(isset(Connection::$tables[$table])) {
            return array_keys(Connection::$tables[$table]);
        }

        // Find all Column names
        $result = $this->rawQuery("PRAGMA table_info($table)", 'array');

        if ($result !== false) {
            foreach ($result as $row) {
                // Get the column name from the results
                $columns[] = $row['name'];
            }
        }

        return $columns;
    }

    /**
     * Get table field/column type
     *
     * @param string $sqliteType
     * @return string
     */
    private static function getTableFieldType($sqliteType)
    {
        switch (strtolower($sqliteType)) {
            case 'integer':
            case 'real':
            case 'numeric':
            case 'boolean':
                return 'int';

            default:
                return 'string';
        }
    }

    /**
     * Get table fields/columns
     *
     * @param string $table
     * @return array
     */
    public function getTableFields($table)
    {
        $columns = array();

        if (empty($table)) {
            throw new \UnexpectedValueException('Parameter should be not empty');
        }

        if(isset(Connection::$tables[$table])) {
            $tableFields = Connection::$tables[$table];

            foreach($tableFields as $field => $row) {
                // Prepare the column entry
                $columns[$field] = array(
                    'type' => self::getTableFieldType($row['type']),
                    'null' => ($row['notnull'] == 0) ? true : false
                );
            }

            return $columns;
        }

        // Find all Column names
        $result = $this->rawQuery("PRAGMA table_info($table)", 'array');

        if ($result !== false) {
            Connection::$tables[$table] = array();

            foreach ($result as $row) {
                $field = $row['name'];

                unset($row['name']);

                Connection::$tables[$table][$field] = $row;

                // Prepare the column entry
                $columns[$field] = array(
                    'type' => self::getTableFieldType($row['type']),
                    'null' => ($row['notnull'] == 0) ? true : false
                );
            }
        }

        return $columns;
    }
}
