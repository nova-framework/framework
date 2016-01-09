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

use \PDO;


class Database
{
    private $linkName = 'default';

    private static $instances = array();


    public static function getInstance($linkName = 'default')
    {
        // Checking if the same
        if (isset(self::$instances[$linkName])) {
            return self::$instances[$linkName];
        }

        $instance = new Database($linkName);

        // Setting Database into $instances to avoid duplication
        self::$instances[$linkName] = $instance;

        return $instance;
    }

    protected function __construct($linkName)
    {
        $this->linkName = $linkName;
    }

    public function getLinkName()
    {
        return $this->linkName;
    }

    public function select($sql, array $params = array(), array $paramTypes = array(), $fetchClass = null, $fetchAll = false)
    {
        $statement = $this->executeQuery($sql, $params, $paramTypes);

        if($statement === false) {
            return false;
        }

        if($fetchAll) {
            return $statement->fetchAll(PDO::FETCH_CLASS, $fetchClass);
        }

        $statement->setFetchMode(PDO::FETCH_CLASS, $fetchClass);

        return $statement->fetch();
    }

    public function insert($table, array $data, array $paramTypes = array())
    {
        $sql = 'INSERT INTO ' .$table .' (' .implode(', ', array_keys($data)) .')
                VALUES (' .implode(', ', array_fill(0, count($data), '?')) .')';

        return $this->executeUpdate($sql, array_values($data), $paramTypes);
    }

    public function update($table, array $data, array $where, array $paramTypes = array())
    {
        $set = array();

        foreach ($data as $column => $value) {
            $set[] = $column . ' = ?';
        }

        $params = array_merge(array_values($data), array_values($where));

        $sql  = 'UPDATE ' . $table . ' SET ' . implode(', ', $set) .'
                 WHERE ' . implode(' = ? AND ', array_keys($where)) .' = ?';

        return $this->executeUpdate($sql, $params, $paramTypes);
    }

    public function delete($table, array $where, array $paramTypes = array())
    {
        $criteria = array();

        foreach (array_keys($where) as $column) {
            $criteria[] = $column .' = ?';
        }

        $sql = 'DELETE FROM ' .$table .' WHERE ' .implode(' AND ', $criteria);

        return $this->executeUpdate($sql, array_values($where), $paramTypes);
    }

    public function lastInsertId()
    {
        return Manager::getConnection($this->linkName)->lastInsertId();
    }

    protected function bindParams($statement, array $params, array $paramTypes = array())
    {
        if(empty($params)) {
            return;
        }

        // Bind the key and values (only if given).
        foreach ($params as $key => $value) {
            $bindKey = $key + 1;

            if(isset($paramTypes[$key])) {
                $statement->bindValue($bindKey, $value, $paramTypes[$key]);

                continue;
            }

            // No parameter Type found, we try our best of to guess it.
            if (is_integer($value)) {
                $statement->bindValue($bindKey, $value, PDO::PARAM_INT);
            }
            else if (is_bool($value)) {
                $statement->bindValue($bindKey, $value, PDO::PARAM_BOOL);
            }
            else if(is_null($value)) {
                $statement->bindValue($bindKey, $value, PDO::PARAM_NULL);
            }
            else {
                $statement->bindValue($bindKey, $value, PDO::PARAM_STR);
            }
        }
    }

    protected function executeQuery($sql, array $params, array $paramTypes = array())
    {
        $link = Manager::getConnection($this->linkName);

        $link->countIncomingQuery();

        // Prepare and get statement from PDO; note that we use the true PDO method 'prepare'
        $statement = $link->prepare($sql);

        // Bind the parameters.
        $this->bindParams($statement, $params, $paramTypes);

        // Execute the statement, return false if fail.
        if (! $statement->execute()) {
            return false;
        }

        // Return the resulted PDO Statement.
        return $statement;
    }

    protected function executeUpdate($sql, array $params, array $paramTypes = array())
    {
        $link = Manager::getConnection($this->linkName);

        $link->countIncomingQuery();

        // Prepare and get statement from PDO; note that we use the true PDO method 'prepare'
        $statement = $link->prepare($sql);

        // Bind the parameters.
        $this->bindParams($statement, $params, $paramTypes);

        if (! $statement->execute()) {
            return false;
        }

        // Row count, affected rows.
        return $statement->rowCount();
    }

}
