<?php


namespace Nova\Database\Service;

use Nova\Database\DatabaseService;
use Nova\Database\Engine\MySQLEngine;
use Nova\Database\EngineFactory;
use Nova\Database\Entity;

/**
 * Class MySQLService
 *
 * @package Core\Database\Service
 */
class MySQLService extends DatabaseService implements Service
{

    /** @var int Fetch method (use \PDO::FETCH_* */
    protected $fetchMethod = \PDO::FETCH_CLASS;

    /** @var null|string Full namespace and class of entity, only when method is FETCH_CLASS */
    protected $fetchClass = null;


    public function __construct($engine = null)
    {
        if ($engine === null)
        {
            $engine = EngineFactory::getEngine();
        }

        $this->driver = EngineFactory::DRIVER_MYSQL;

        parent::__construct($engine);

        /** @var MySQLEngine engine */
        $this->engine = $engine;
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
        if (!is_array($entity)) {
            $entity = array($entity);
        }

        // Loop and insert
        foreach($entity as $idx => $entit)
        {
            // Insert
            $result = $this->engine->executeInsert(DB_PREFIX . $this->table, get_object_vars($entit));
            if ($result === false) {
                // On error, return inmidiate.
                return false;
            }

            // If only one Primary Key, we will set it in the entity.
            if (count($this->primaryKeys) == 1 && $entit->{$this->primaryKeys[0]} == null) {
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
     * @param $bind array
     * @return false|Entity[]|object
     * @throws \Exception
     */
    public function read($sql, $bind = array())
    {
        return $this->engine->executeQuery($sql, $bind, $this->fetchMethod, $this->fetchClass);
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
    public function update($entity, $limit = 1)
    {
        $primaryValues = array();

        foreach($this->primaryKeys as $pk) {
            $primaryValues[$pk] = $entity->{$pk};
        }

        $result = $this->engine->executeUpdate(DB_PREFIX . $this->table, get_object_vars($entity), $primaryValues, $limit);
        if ($result === false) {
            return false;
        }

        // Primary Key, put it back into the entity.
        if (count($this->primaryKeys) == 1 && $entity->{$this->primaryKeys[0]} == null) {
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
    public function delete($entity, $limit = 1)
    {
        $primaryValues = array();

        foreach($this->primaryKeys as $pk) {
            $primaryValues[$pk] = $entity->{$pk};
        }

        return $this->engine->executeDelete(DB_PREFIX . $this->table, $primaryValues, $limit) !== false;
    }
}
