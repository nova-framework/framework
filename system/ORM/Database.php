<?php
/**
 * Database - Incapsulate and Extends the \Nova\Database\Connection to support the unnamed parameters binding.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 9th, 2016
 */

namespace Nova\ORM;

use Nova\Database\Manager;
use Nova\Database\Connection;


class Database
{
    private $connection;

    private $instances = array();


    public function __construct($linkName = 'default')
    {
        $this->connection = Manager::getConnection($linkName);
    }

    public static function getInstance($linkName = 'default')
    {
        // Checking if the same
        if (isset(self::$instances[$linkName])) {
            return self::$instances[$linkName];
        }

        $instance = new Database($linkName);

        // Setting Database into $instances to avoid duplication
        self::$instances[$linkName] = $instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function select($sql, array $params = array(), array $paramTypes = array(), $returnType = null, $fetchAll = false)
    {
        return false;
    }

    public function insert($table, array $data, array $paramTypes = array(), $transaction = false)
    {
        return false;
    }

    public function update($table, array $data, array $where, array $paramTypes = array())
    {
        return false;
    }

    public function delete($table, array $where, array $paramTypes = array())
    {
        return false;
    }

    /**
     * Provide direct access to any of \Nova\Database\Connection methods.
     *
     * @param $name
     * @param $params
     */
    public function __call($method, $params = null)
    {
        if (method_exists($this->connection, $method)) {
            return call_user_func_array(array($this->connection, $method), $params);
        }
    }

}
