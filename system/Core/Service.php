<?php
/**
 * Service - base service
 *
 * @author Tom Valk
 * @date September 29, 2015
 * @date December 13, 2015
 */

namespace Smvc\Core;

use Smvc\Helpers\Database;

/**
 * Abstract Class Service, Your service classes need to extend this abstract class
 *
 * @package Core
 */
abstract class Service
{
    /**
     * Database helper
     * @var Database
     */
    protected $db;


    protected $table;
    protected $primaryKey;
    protected $class;

    /**
     * Service constructor. Make sure to implement this in your classes that extend this class.
     * Give your table name (Without prefix), primary key name and model class (without full namespace)
     *
     * @param $table string Table name, without prefix!
     * @param $primaryKey string Primary key column name
     * @param $class string Model class name, no full namespace!
     */
    public function __construct($table, $primaryKey, $class)
    {
        $this->db = Database::get();

        $this->table = $table;
        $this->primaryKey = $primaryKey;
        $this->class = $class;
    }

    /**
     * Create operation - Create object in database
     * @param \Core\Model $object
     * @return int inserted id
     */
    public function create($object)
    {
        $result = $this->db->insert(PREFIX . $this->table, get_object_vars($object));
        if ($result === false) {
            return false;
        }

        $object->{$this->primaryKey} = $result;
        return $result;
    }

    /**
     * @param string $sql SQL for select query. Should be able to bind result to object class.s
     * @param array $bind
     * @return \Core\Model[] Readed objects of type
     */
    public function read($sql, $bind = array())
    {
        return $this->db->select($sql, $bind, \PDO::FETCH_CLASS, '\App\Models\\'.$this->class);
    }

    /**
     * Get object by primary key
     * @param $id mixed Primary key value
     * @return null|\Core\Model
     */
    public function get($id)
    {
        $results = $this->db->select("SELECT * FROM " . PREFIX . $this->table . " WHERE " . $this->primaryKey . " = :pk", array(':pk'=>$id), \PDO::FETCH_CLASS, '\App\Models\\'.$this->class);
        if (count($results) > 0) {
            return $results[0];
        }
        return null;
    }

    /**
     * Update current object in database - Please be sure you don't update the primary key!
     * @param \Core\Model $object
     * @return int|false updated id, false on error
     */
    public function update($object)
    {
        return $this->db->update(PREFIX . $this->table, get_object_vars($object), array($this->primaryKey => $object->{$this->primaryKey}));
    }

    /**
     * Delete object from database
     * @param \Core\Model $object
     * @param int $limit limit delete operation
     * @return boolean Result of deleting
     */
    public function delete($object, $limit = 1)
    {
        return $this->db->delete(PREFIX . $this->table, array($this->primaryKey => $object->{$this->primaryKey}), $limit);
    }
}
