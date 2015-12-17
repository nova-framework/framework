<?php


namespace Smvc\Database\Service;

use Smvc\Database\DatabaseService;
use Smvc\Database\Engine\MySQLEngine;
use Smvc\Database\EngineFactory;
use Smvc\Database\Entity;

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
     * Create the entity in the database. Will try to insert it into the database
     * Can throw Exceptions on failure or return false.
     *
     * On success it will return the entity including the (optional) inserted ID (primary key, when only one)
     *
     * @param $entity Entity
     * @return false|Entity
     * @throws \Exception
     */
    public function create($entity)
    {
        $result = $this->engine->insert(DB_PREFIX . $this->table, get_object_vars($entity));
        if ($result === false) {
            return false;
        }

        if (count($this->primaryKeys) == 1) {
            $entity->{$this->primaryKeys[0]} = $result;
        }else{
            // TODO: We can't map this, we don't get multiple primary keys back unfortunately. Solution still needed
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
        return $this->engine->select($sql, $bind, $this->fetchMethod, $this->fetchClass);
    }

    /**
     * Will update the entity in the database. You could also give an array with entities. We will automaticly detect
     * if the given $entity is an array or just one object.
     *
     * Make sure you don't change your primary keys! As this will be used to execute the update with
     * For safety it will default limit on 1 row only, you can override it but be warned on this!
     *
     * @param $entity Entity
     * @param $limit int Limit of changes, may not be effective on every driver! Default 1. TODO: Will not be used currently
     * @return false|Entity
     * @throws \Exception
     */
    public function update($entity, $limit = 1)
    {
        $primaryValues = array();

        foreach($this->primaryKeys as $pk) {
            $primaryValues[$pk] = $entity->{$pk};
        }

        $result = $this->engine->update(DB_PREFIX . $this->table, get_object_vars($entity), $primaryValues);
        if ($result === false) {
            return false;
        }

        if (count($this->primaryKeys) == 1) {
            $entity->{$this->primaryKeys[0]} = $result;
        }else{
            // TODO: We can't map this, we don't get multiple primary keys back unfortunately. Solution still needed
        }

        return $entity;
    }

    /**
     * Delete an entity from the database. Can also handle multiple entities with an array given in the $entity parameter
     *
     * For safety it will default limit on 1 row only, you can override it but be warned on this!
     *
     * @param $entity Entity
     * @param $limit int Limit of changes, may not be effective on every driver! Default 1
     * @return boolean successful delete?
     * @throws \Exception
     */
    public function delete($entity, $limit = 1)
    {
        $primaryValues = array();

        foreach($this->primaryKeys as $pk) {
            $primaryValues[$pk] = $entity->{$pk};
        }

        return $this->engine->delete(DB_PREFIX . $this->table, $primaryValues, $limit) !== false;
    }
}
