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
use Nova\Cache\Manager as CacheManager;


class SQLite extends Connection
{

    /**
     * SQLiteEngine constructor.
     * Please use the \Nova\Database\Manager to maintain instances of the drivers.
     *
     * @param $config array
     * @throws \PDOException
     */
    public function __construct(array $config)
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

    private function getTableFieldData($data)
    {
        return array(
            'type' => self::getTableFieldType($row['Type']),
            'null' => ($row['notnull'] == 0) ? true : false
        );
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
                $columns[$field] = $this->getTableFieldData($row);
            }

            return $columns;
        }

        // Prepare the Cache Token.
        $token = 'sqlite_table_fields_' .md5($table);

        // Setup the Cache instance.
        $cache = CacheManager::getCache();

        // Get the Table Fields, using the Framework Caching.
        $fields = $cache->get($token);

        if($fields === null) {
            $fields = array();

            // Find all Column names
            $sql = "PRAGMA table_info($table)";

            // Get the current Time.
            $time = microtime(true);

            $result = $this->rawQuery($sql, 'array');

            if ($result !== false) {
                foreach ($result as $row) {
                    $field = $row['name'];

                    unset($row['name']);

                    $fields[$field] = $row;

                    // Prepare the column entry
                    $columns[$field] = $this->getTableFieldData($row);
                }
            }

            $this->logQuery($sql, array(), $time);

            // Write to Cache 300 seconds = 5 minutes
            $cache->set($token, $fields, 300);
        } else {
            foreach($fields as $field => $row) {
                $columns[$field] = $this->getTableFieldData($row);
            }
        }

        // Write to local static Cache
        Connection::$tables[$table] = $fields;

        return $columns;
    }
}
