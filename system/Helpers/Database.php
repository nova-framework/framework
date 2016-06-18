<?php
/**
 * Database Helper
 *
 * @author David Carr - dave@novaframework.com
 * @version 3.0
 */

namespace Helpers;

use Core\Config;

use DB;

use PDO;


/**
 * Implements a Database helper.
 */
class Database
{
    /**
     * The Database Connection name.
     *
     * @var string
     */
    protected $connection;

    /**
     * @var array Array of saved databases for reusing
     */
    protected static $instances = array();

    /**
     * The connection resolver instance.
     *
     * @var \Database\ConnectionResolverInterface
     */
    protected static $resolver;


    /**
     * Constructor
     *
     * @param  string $connection
     * @return void
     */
    protected function __construct($connection)
    {
        $this->connection = $connection;
    }

    /**
     * Static method get
     *
     * @param  array $group
     * @return Helpers\Database
     */
    public static function get($name = null)
    {
        $name = $name ?: Config::get('database.default');

        // Check if the instance is the same.
        if (isset(self::$instances[$name])) {
            return self::$instances[$name];
        }

        // Set the Database into $instances to avoid any potential duplication.
        return self::$instances[$name] = new static($name);
    }

    /**
     * Run raw sql queries.
     *
     * @param  string $sql sql command
     * @return return query
     */
    public function raw($sql)
    {
        return $this->getPdo()->query($sql);
    }

    /**
     * Select records from the database.
     *
     * @param  string $sql       sql query
     * @param  array  $array     named params
     * @param  object $fetchMode
     * @param  string $class     class name
     * @return array            returns an array of records
     */
    public function select($sql, $array = array(), $fetchMode = PDO::FETCH_OBJ, $class = '')
    {
        $stmt = $this->getPdo()->prepare($sql);

        foreach ($array as $key => $value) {
            if (is_int($value)) {
                $stmt->bindValue("$key", $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue("$key", $value);
            }
        }

        $stmt->execute();

        if ($fetchMode === PDO::FETCH_CLASS) {
            return $stmt->fetchAll($fetchMode, $class);
        } else {
            return $stmt->fetchAll($fetchMode);
        }
    }

    /**
     * Insert method.
     *
     * @param  string $table table name
     * @param  array $data  array of columns and values
     */
    public function insert($table, $data)
    {
        ksort($data);

        $fieldNames = implode(',', array_keys($data));
        $fieldValues = ':'.implode(', :', array_keys($data));

        $stmt = $this->getPdo()->prepare("INSERT INTO $table ($fieldNames) VALUES ($fieldValues)");

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();

        return $this->lastInsertId();
    }

    /**
     * Update method.
     *
     * @param  string $table table name
     * @param  array $data  array of columns and values
     * @param  array $where array of columns and values
     */
    public function update($table, $data, $where)
    {
        ksort($data);

        $fieldDetails = null;

        foreach ($data as $key => $value) {
            $fieldDetails .= "$key = :field_$key,";
        }

        $fieldDetails = rtrim($fieldDetails, ',');

        $whereDetails = null;

        $i = 0;

        foreach ($where as $key => $value) {
            if ($i == 0) {
                $whereDetails .= "$key = :where_$key";
            } else {
                $whereDetails .= " AND $key = :where_$key";
            }
            $i++;
        }

        $whereDetails = ltrim($whereDetails, ' AND ');

        $stmt = $this->getPdo()->prepare("UPDATE $table SET $fieldDetails WHERE $whereDetails");

        foreach ($data as $key => $value) {
            $stmt->bindValue(":field_$key", $value);
        }

        foreach ($where as $key => $value) {
            $stmt->bindValue(":where_$key", $value);
        }

        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * Delete method.
     *
     * @param  string $table table name
     * @param  array $where array of columns and values
     * @param  integer   $limit limit number of records
     */
    public function delete($table, $where, $limit = 1)
    {
        ksort($where);

        $whereDetails = null;

        $i = 0;

        foreach ($where as $key => $value) {
            if ($i == 0) {
                $whereDetails .= "$key = :$key";
            } else {
                $whereDetails .= " AND $key = :$key";
            }
            $i++;
        }

        $whereDetails = ltrim($whereDetails, ' AND ');

        // If the limit is a number, use a limit on the query.
        if (is_numeric($limit)) {
            $uselimit = "LIMIT $limit";
        }

        $stmt = $this->getPdo()->prepare("DELETE FROM $table WHERE $whereDetails $uselimit");

        foreach ($where as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * Truncate table.
     *
     * @param  string $table table name
     */
    public function truncate($table)
    {
        return $this->getPdo()->exec("TRUNCATE TABLE $table");
    }

    /**
     * Get a PDO instance from Database API.
     *
     * @return PDO
     */
    public function getPdo()
    {
        $connection = $this->resolveConnection($this->connection);

        return $connection->getPdo();
    }

    /**
     * Resolve a connection instance.
     *
     * @param  string  $connection
     * @return \Database\Connection
     */
    public static function resolveConnection($connection = null)
    {
        return static::$resolver->connection($connection);
    }

    /**
     * Get the connection resolver instance.
     *
     * @return \Database\ConnectionResolverInterface
     */
    public static function getConnectionResolver()
    {
        return static::$resolver;
    }

    /**
     * Set the connection resolver instance.
     *
     * @param  \Database\ConnectionResolverInterface  $resolver
     * @return void
     */
    public static function setConnectionResolver(Resolver $resolver)
    {
        static::$resolver = $resolver;
    }

    /**
     * Unset the connection resolver for models.
     *
     * @return void
     */
    public static function unsetConnectionResolver()
    {
        static::$resolver = null;
    }

     /**
     * Magic Method for handling dynamic functions.
     *
     * @param  string  $method
     * @param  array   $params
     * @return mixed|void
     */
    public function __call($method, $params)
    {
        $instance = $this->getPdo();

        return call_user_func_array(array($instance, $method), $params);
    }
}
