<?php
/**
 * MySQL Engine.
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 27th, 2015
 */

namespace Nova\Database\Driver;

use Nova\Database\Connection;
use Nova\Database\Manager;

class MySQL extends Connection
{

    /**
     * MySQLEngine constructor.
     * Please use the Factory to maintain instances of the drivers.
     *
     * @param $config array
     *
     * @throws \PDOException
     */
    public function __construct($config)
    {
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
        } else {
            $charsetStr = ($config['charset'] == 'auto') ? "" : ";charset=" . $config['charset'];
        }

        // Prepare the PDO's options.
        if (isset($config['compress']) && ($config['compress'] === true)) {
            $options = array(\PDO::MYSQL_ATTR_COMPRESS => true);
        } else {
            $options = array();
        }

        // Prepare the PDO's DSN
        $dsn = "mysql:host=" .$config['host'] .";port=" .$config['port'] .";dbname=" .$config['dbname'] .$charsetStr;

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
     * Get the columns names for the specified Database Table.
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
        $result = $this->rawQuery("SHOW COLUMNS FROM $table", 'array');

        if ($result !== false) {
            foreach ($result as $row) {
                // Get the column name from the results
                $columns[] = $row['Field'];
            }
        }

        return $columns;
    }

    /**
     * Get table column/field type
     *
     * @param string $mysqlType
     * @return string
     */
    private static function getTableFieldType($mysqlType)
    {
        if (preg_match("/^([^(]+)/", $mysqlType, $match)) {
            switch (strtolower($match[1])) {
                case 'tinyint':
                case 'smallint':
                case 'mediumint':
                case 'int':
                case 'bigint':
                case 'float':
                case 'double':
                case 'decimal':
                    return 'int';

                default:
                    return 'string';
            }
        }

        return 'string';
    }

    /**
     * Get table fields/columns
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
                    'type' => self::getTableFieldType($row['Type']),
                    'null' => ($row['Null'] == 'YES') ? true : false
                );
            }

            return $columns;
        }

        // Find all Column names
        $result = $this->rawQuery("SHOW COLUMNS FROM $table", 'array');

        $cid = 0;

        if ($result !== false) {
            Connection::$tables[$table] = array();

            foreach ($result as $row) {
                $field = $row['Field'];

                unset($row['Field']);

                $row['CID'] = $cid;

                Connection::$tables[$table][$field] = $row;

                // Prepare the column entry
                $columns[$field] = array(
                    'type' => self::getTableFieldType($row['Type']),
                    'null' => ($row['Null'] == 'YES') ? true : false
                );

                $cid++;
            }
        }

        return $columns;
    }

}
