<?php
/**
 * Abstract Database Service.
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @version 3.0
 * @date December 19th, 2015
 */

namespace Nova\Database;

use Nova\Database\Engine;
use Nova\Core\Service as CoreService;


/**
 * Class DatabaseService.
 * @package Core\Database
 */
abstract class Service extends CoreService
{
    protected $fetchClass = null;

    /** @var Engine database engine we will use. */
    protected $engine;

    /** @var string Table name. Don't use prefix here! */
    protected $table;

    /** @var string[]|array Primary keys. */
    protected $primaryKeys;


    /**
     * Constructor
     */
    public function __construct()
    {

    }

    /**
     * Set engine for this service.
     * @param Engine $engine
     */
    public function engine($engine = null)
    {
        if($engine === null) {
            return $this->engine;
        }

        if (! $engine instanceof Engine) {
            throw new \UnexpectedValueException("Engine should be an instance of one of the Engines!");
        }

        $this->engine = $engine;
    }

    /**
     * Get database table for this service.
     */
    public function table()
    {
        return $this->table;
    }

    /**
     * Get Entity className (fetchClass) for this service.
     */
    public function entity()
    {
        return $this->fetchClass;
    }

    /**
     * Create the entity (or entities) in the database. Will try to insert it into the database
     * Can throw Exceptions on failure or return false.
     *
     * On success it will return the entity including the (optional) inserted ID (primary key, when only one)
     *
     * @param $entity Entity|Entity[] One or multiple entit(y|ies) to create in the database
     * @return false|Entity
     * @throws \Exception
     */
    public function create($entity)
    {
        // If it isn't already an array, make it an array, to keep code simple.
        if (! is_array($entity)) {
            $entity = array($entity);
        }

        // Loop and insert
        foreach($entity as $idx => $what)
        {
            // Insert
            $result = $this->engine->insert(DB_PREFIX . $this->table, get_object_vars($what));

            if ($result === false) {
                // On error, return inmidiate.
                return false;
            }

            // If only one Primary Key, we will set it in the entity.
            if ((count($this->primaryKeys) == 1) && ($what->{$this->primaryKeys[0]} == null)) {
                $entity[$idx]->{$this->primaryKeys[0]} = $result;
            }

        }

        // Return same format as before
        if (count($entity) === 1) {
            return $entity[0];
        }

        return $entity;
    }

    /**
     * Read entities with the $sql query, must always give a full query, including the prefix and where's.
     * Use the mapping of the driver, for mysql this would be valid:
     * SELECT * FROM cars WHERE id = :id
     *
     * Make sure you are giving the parameters in the $bind parameter.
     *
     * @param $sql string
     * @param array $bindParams
     * @return false|Entity[]|object
     * @throws \Exception
     */
    public function read($sql, $bindParams = array())
    {
        if ($this->fetchClass === null) {
            throw new \Exception("No fetchClass is given while calling READ method");
        }

        return $this->engine->selectAll($sql, $bindParams, \PDO::FETCH_CLASS, $this->fetchClass);
    }

    /**
     * Will update the entity in the database. You could also give an array with entities. We will automaticly detect
     * if the given $entity is an array or just one object.
     *
     * Make sure you don't change your primary keys! As this will be used to execute the update with
     * For safety it will default limit on 1 row only, you can override it but be warned on this!
     *
     * @param $entity Entity
     * @param $limit int Limit of changes, may not be effective on every driver! Default 1.
     * @return false|Entity
     * @throws \Exception
     */
    public function update($entity)
    {
        $primaryValues = array();

        foreach($this->primaryKeys as $pk) {
            $primaryValues[$pk] = $entity->{$pk};
        }

        $result = $this->engine->update(DB_PREFIX .$this->table, get_object_vars($entity), $primaryValues);

        if ($result === false) {
            return false;
        }

        // Primary Key, put it back into the entity.
        if ((count($this->primaryKeys) == 1) && ($entity->{$this->primaryKeys[0]} == null)) {
            $entity->{$this->primaryKeys[0]} = $result;
        }

        return $entity;
    }

    /**
     * Delete an entity from the database. Can also handle multiple entities with an array given in the $entity parameter
     *
     * For safety it will limit on 1 row only by default, you can disable by giving null into the limit.
     *
     * @param $entity Entity
     * @param $limit int|null Limit of changes, may not be effective on every driver! Default 1. Null for infinity.
     * @return boolean successful delete?
     * @throws \Exception
     */
    public function delete($entity)
    {
        $primaryValues = array();

        foreach($this->primaryKeys as $pk) {
            $primaryValues[$pk] = $entity->{$pk};
        }

        $result = $this->engine->delete(DB_PREFIX .$this->table, $primaryValues);

        if ($result === false) {
            return false;
        }

        return true;
    }
}
