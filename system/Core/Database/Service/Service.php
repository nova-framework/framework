<?php


namespace Core\Database\Service;
use Core\Database\Entity;

/**
 * Interface Service for Database Services
 * Use this to create classes in \Core\Database\Service
 *
 * @package Core\Database\Service
 */
interface Service
{
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
    public function create($entity);


    /**
     * Read entities with the $sql query, must always give a full query, including the prefix and where's.
     * Use the mapping of the driver, for mysql this would be valid:
     * SELECT * FROM cars WHERE id = :id
     *
     * Make sure you are giving the parameters in the $bind parameter.
     *
     * @param $sql string
     * @param $bind array
     * @return false|Entity[]
     * @throws \Exception
     */
    public function read($sql, $bind = array());


    /**
     * Will update the entity in the database. You could also give an array with entities. We will automaticly detect
     * if the given $entity is an array or just one object.
     *
     * Make sure you don't change your primary keys! As this will be used to execute the update with
     * For safety it will default limit on 1 row only, you can override it but be warned on this!
     *
     * @param $entity Entity|Entity[]
     * @param $limit int Limit of changes, may not be effective on every driver! Default 1
     * @return false|Entity
     * @throws \Exception
     */
    public function update($entity, $limit = 1);


    /**
     * Delete an entity from the database. Can also handle multiple entities with an array given in the $entity parameter
     *
     * For safety it will default limit on 1 row only, you can override it but be warned on this!
     *
     * @param $entity Entity|Entity[]
     * @param $limit int Limit of changes, may not be effective on every driver! Default 1
     * @return boolean successful delete?
     * @throws \Exception
     */
    public function delete($entity, $limit = 1);
}