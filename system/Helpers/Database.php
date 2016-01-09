<?php
/**
 * Database Helper
 *
 * @author David Carr - dave@daveismyname.com
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date June 27, 2014
 * @date updated Sept 19, 2015
 */

namespace Nova\Helpers;

use Nova\Database\Manager;
use Nova\Database\Connection;

/**
 * Extending PDO to use custom methods.
 *
 * @deprecated since v3.0
 */
class Database
{
    // The real Connection instance used.
    protected $db = null;

    /**
     * @var array Array of saved databases for reusing
     */
    protected static $instances = array();

    /**
     * Static method get
     *
     * @param  array $group
     * @return \helpers\database
     */
    public static function get($linkName = false)
    {
        if(is_array($linkName)) {
            throw new \Exception(__d('system', 'Invalid Configuration on the Legacy Helper');
        }

        // Adjust the linkName value, if case.
        $linkName = $linkName ? $linkName : 'default';

        // Checking if the same
        if (isset(self::$instances[$linkName])) {
            return self::$instances[$linkName];
        }

        $instance = new Database($linkName);

        // Setting Database into $instances to avoid duplication
        self::$instances[$linkName] = $instance;

        return $instance;
    }

    protected __construct($linkName)
    {
        $this->db = Manager::getConnection($linkName);
    }

    /**
     * run raw sql queries
     * @param  string $sql sql command
     * @return return query
     */
    public function raw($sql)
    {
        return $this->db->rawQuery($sql);
    }

    /**
     * method for selecting records from a database
     * @param  string $sql       sql query
     * @param  array  $array     named params
     * @param  object $fetchMode
     * @param  string $class     class name
     * @return array            returns an array of records
     */
    public function select($sql, $array = array(), $fetchMode = PDO::FETCH_OBJ, $class = '')
    {
        if($fetchMode == PDO::FETCH_OBJ) {
            $returnType = 'object';
        }
        else if($fetchMode == PDO::FETCH_CLASS) {
            if(empty($class)) {
                throw new \Exception(__d('system', 'No valid Class is given'));
            }

            $returnType = $class;
        }
        else {
            $returnType = 'array';
        }

        // Pre-process the $array to simulate the make the old Helper behavior.
        $where = array();
        $paramTypes = array();

        foreach($array as $field => $value) {
            // Strip the character ':', if it exists in the first position of $field.
            if(substr($field, 0, 1) == ':') {
                $field = substr($field, 1);
            }

            $where[$field] = $value;

            // Prepare the old style entry into paramTypes.
            $paramTypes[$field] = is_integer($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
        }

        return $this->db->select($sql, $where, $paramTypes, $returnType, true);
    }

    /**
     * insert method
     * @param  string $table table name
     * @param  array $data  array of columns and values
     */
    public function insert($table, $data)
    {
        ksort($data);

        // Pre-process the $data variable to simulate the make the old Helper behavior.
        $paramTypes = array();

        foreach($data as $field => $value) {
            // Prepare the compat entry into paramTypes.
            $paramTypes[$field] = is_integer($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
        }

        return $this->db->insert($table, $data, $paramTypes);
    }

    /**
     * update method
     * @param  string $table table name
     * @param  array $data  array of columns and values
     * @param  array $where array of columns and values
     */
    public function update($table, $data, $where)
    {
        ksort($data);

        // Pre-process the $data and $where variables to simulate the old Helper behavior.
        $paramTypes = array();

        foreach($data as $field => $value) {
            // Prepare the compat entry into paramTypes.
            $paramTypes[$field] = is_integer($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
        }

        foreach($where as $field => $value) {
            // Prepare the compat entry into paramTypes.
            $paramTypes[$field] = is_integer($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
        }

        return $this->db->update($table, $data, $where, $paramTypes);
    }

    /**
     * Delete method
     *
     * @param  string $table table name
     * @param  array $where array of columns and values
     * @param  integer   $limit limit number of records
     */
    public function delete($table, $where, $limit = 1)
    {
        ksort($where);

        // Pre-process the $where variable to simulate the old Helper behavior.
        $paramTypes = array();

        foreach($where as $field => $value) {
            // Prepare the compat entry into paramTypes.
            $paramTypes[$field] = is_integer($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
        }

        return $this->db->delete($table, $where, $paramTypes);
    }

    /**
     * truncate table
     * @param  string $table table name
     */
    public function truncate($table)
    {
        return $this->db->truncate($table);
    }

    /**
     * Provide direct access to any of \Nova\Database\Connection methods.
     *
     * @param $name
     * @param $params
     */
    public function __call($method, $params = null)
    {
        if (method_exists($this->db, $method)) {
            return call_user_func_array(array($this->db, $method), $params);
        }
    }
}
