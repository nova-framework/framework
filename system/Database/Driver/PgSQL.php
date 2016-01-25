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
use Nova\Cache\Manager as CacheManager;


class PgSQL extends Connection
{

    /**
     * MySQLEngine constructor.
     * Please use the \Nova\Database\Manager to maintain instances of the drivers.
     *
     * @param $config array
     *
     * @throws \PDOException
     */
    public function __construct(array $config)
    {
        // Check for valid Config.
        if (! is_array($config)) {
            throw new \UnexpectedValueException('Parameter should be an Array');
        }

        // Default port if no port is provided.
        if (! isset($config['port'])) {
            $config['port'] = 5432;
        }

        // Prepare the options.
        $options = isset($config['options']) ? $config['options'] : array();

        // Prepare the PDO's DSN
        $dsn = "pgsql:host=" .$config['host'] .";port=" .$config['port'] .";dbname=" .$config['dbname'];

        // Execute the Parent Constructor.
        parent::__construct($dsn, $config, $options);

        // Post processing.
        if (isset($config['charset'])) {
            $this->prepare("SET NAMES '{$config['charset']}'")->execute();
        }

        if (isset($config['schema'])) {
            $this->prepare("SET search_path TO '{$config['schema']}'")->execute();
        }
    }

    /**
     * Get the name of the driver
     * @return string
     */
    public function getDriverName()
    {
        return __d('system', 'PostgreSQL Driver');
    }

    /**
     * Get driver code, used in config as driver string.
     * @return string
     */
    public function getDriverCode()
    {
        return Manager::DRIVER_PGSQL;
    }

    /**
     * Truncate table
     * @param  string $table table name
     * @return int number of rows affected
     */
    public function truncate($table)
    {
        $sql = "TRUNCATE TABLE $table";

        // Get the current Time.
        $time = microtime(true);

        $result = $this->exec($sql);

        $this->logQuery($sql, $time);

        return $result;
    }

    /**
     * Get table column/field type
     *
     * @param string $pgsqlType
     * @return string
     */
    private static function getTableFieldType($pgsqlType)
    {
        if (preg_match("/^([^(]+)/", $pgsqlType, $match)) {
            switch (strtolower($match[1])) {
                case 'smallint':
                case 'integer':
                case 'bigint':
                case 'real':
                case 'double':
                case 'decimal':
                case 'numeric':
                case 'serial':
                case 'bigserial':
                    return 'int';

                default:
                    return 'string';
            }
        }

        return 'string';
    }

    private function getTableFieldData($data)
    {
        $isNullable = strtoupper($data['is_nullable']);

        return array(
            'type' => self::getTableFieldType($data['data_type']),
            'null' => ($isNullable == 'YES') ? true : false
        );
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
            $fields = Connection::$tables[$table];

            foreach($fields as $field => $row) {
                $columns[$field] = $this->getTableFieldData($row);
            }

            return $columns;
        }

        // Prepare the Cache Token.
        $token = 'pgsql_table_fields_' .md5($table);

        // Setup the Cache instance.
        $cache = CacheManager::getCache();

        // Get the Table Fields, using the Framework Caching.
        $fields = $cache->get($token);

        if($fields === null) {
            $fields = array();

            // Find all Column names
            $sql = "SELECT column_name, data_type, is_nullable FROM information_schema.columns WHERE table_name ='$table';";

            // Get the current Time.
            $time = microtime(true);

            $result = $this->rawQuery($sql, 'array', false);

            $cid = 0;

            if ($result !== false) {
                foreach ($result as $row) {
                    $field = $row['column_name'];

                    unset($row['column_name']);

                    $row['CID'] = $cid;

                    $fields[$field] = $row;

                    // Prepare the column entry
                    $columns[$field] = $this->getTableFieldData($row);

                    $cid++;
                }
            }

            $this->logQuery($sql, $time);

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
