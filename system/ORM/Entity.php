<?php
/**
 * Abstract ORM Entity.
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @version 3.0
 * @date December 19th, 2015
 */

namespace Nova\ORM;

use Doctrine\DBAL\Query\QueryBuilder;
use Nova\DBAL\Connection;
use Nova\DBAL\Manager as DBALManager;
use Nova\ORM\Annotation\Column;
use \PDO;

/**
 * Class Entity, can be extended with your database entities.
 *
 * @template <T> Entity generics, type of the entity itself.
 */
abstract class Entity
{
    /**
     * Hold the state of the current Entity. Will be used to determinate if INSERT or UPDATE is needed.
     *
     *  0 - Unsaved
     *  1 - Fetched, already in database
     *
     * @var int
     */
    public $_state = 1;

    /**
     * Primary Key reference or value
     */
    private $_id = null;

    /**
     * Will be called each time a static call is made, to check if the entity is indexed.
     *
     * @param $method
     * @param $parameters
     *
     * @codeCoverageIgnore
     */
    public static function __callStatic($method, $parameters){
        if (method_exists(__CLASS__, $method)) {
            self::discoverEntity();
            forward_static_call_array(array(__CLASS__,$method),$parameters);
        }
    }

    /**
     * Index entity if needed.
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    private static function discoverEntity()
    {
        Structure::indexEntity(static::class);
    }


    public function __construct()
    {
        static::discoverEntity();
        $this->_state = 0;
    }


    /**
     * Get Link instance.
     *
     * @return Connection
     */
    private static function getLink()
    {
        return DBALManager::getConnection();
    }


    /**
     * Query Builder for finding.
     *
     * @return QueryBuilder
     *
     * @codeCoverageIgnore
     */
    public static function getQueryBuilder()
    {
        return self::getLink()->createQueryBuilder();
    }

    /**
     * Get Entity properties as assoc array. Useful for insert, update or just debugging.
     *
     * @param bool $types Get types of columns. Default false.
     * @return array
     */
    public function getColumns($types = false)
    {
        $columns = Structure::getTableColumns($this);

        $data = array();
        foreach($columns as $column) {
            if ($types) {
                $data[$column->name] = $column->getPdoType();
            } else {
                $data[$column->name] = $this->{$column->getPropertyField()};
            }
        }

        return $data;
    }

    /**
     * Get Primary Key data array or type array.
     *
     * @param bool $types Get types of primary columns. Default false.
     * @return array
     * @throws \Exception
     */
    public function getPrimaryKey($types = false)
    {
        $primaryKey = Structure::getTablePrimaryKey(static::class);

        if ($primaryKey === false) {
            throw new \Exception("Primary Keys can't be detected!");
        }

        $data = array();
        if ($types) {
            $data[$primaryKey->name] = $primaryKey->getPdoType();
        } else {
            $data[$primaryKey->name] = $this->{$primaryKey->getPropertyField()};
        }

        return $data;
    }


    /**
     * Start a query.
     *
     * @return Query
     */
    public static function query()
    {
        return new Query(static::class);
    }

    /**
     * Find multiple entities by searching on the primary key values given.
     *
     * @param array $ids Array of primary key values possible to return.
     * @return array<T>|Entity[]|false Array of entities or false on not found.
     * @throws \Exception Exceptions are thrown when errors occur.
     */
    public static function findMany($ids)
    {
        if (! is_array($ids)) {
            throw new \UnexpectedValueException("IDs should be an array of primary key values!");
        }

        $primaryKey = Structure::getTablePrimaryKey(static::class);
        if ($primaryKey === false) {
            throw new \Exception("Primary Key can't be detected!");
        }
        // Only get column name for the primary key
        $primaryKey = $primaryKey->name;

        /** @var Entity[] $result */
        $result = static::query()->where(array($primaryKey => array("IN" => $ids)))->all();

        // Return results
        return $result;
    }


    /**
     * Find entity by searching for the exact ID key. Or create query and return query.
     *
     * @param string|int|null $id Primary key value. Ignore for query building.
     * @return Entity|Query|false
     *
     * @throws \Exception
     */
    public static function find($id = null)
    {
        if ($id === null) {
            return static::query();
        }

        $many = static::findMany(array($id));

        if (count($many) <> 1) {
            return false;
        }
        $result = $many[0];

        if($result instanceof Entity) {
            return $result;
        }
        return false;
    }


    /**
     * Find a single entity in database by searching for the given criteria.
     *
     * This will make a query and execute it, return an unique entity with entities or false/Exception on error.
     *
     * @param array $criteria Array of key => value where the key is the column name and the value is the required value.
     * You could also use one of the custom comparators, like:
     *  - column => ['=' => value], column => ['<' => value], column => ['LIKE' => value] or column => ['>=' => value]
     *
     * When using a single criteria you could use this parameter as the column and the other 2 parameters as operator and value.
     *
     * @param null|string $operator Operator to use when having a single criteria.
     * @param null|string $value Value (or multiple when using IN as a operator) to have a single criteria.
     *
     * @return Entity|Entity<T> Single entity.
     * @throw \Exception Exceptions when having errors while preparing, fetching, connecting or parsing.
     */
    public static function findBy($criteria, $operator = null, $value = null)
    {
        if (! is_array($criteria) && ($operator == null && $value == null))
        {
            throw new \UnexpectedValueException("Criteria should be an array! Or use the shorthand syntax.");
        }

        // Return result
        return static::query()->where($criteria, $operator, $value)->one();
    }

    /**
     * Insert or update the entity in the database.
     *
     * @return int Affected rows.
     * @throws \Exception Throws exceptions on error.
     */
    public function save()
    {
        if ($this->_state == 0) {
            // Insert
            $result = static::getLink()->insert(Structure::getTable($this)->getFullTableName(), $this->getColumns(), $this->getColumns(true));

            // Primary Key
            $this->_id = static::getLink()->lastInsertId();

            /** @var Column $primaryKey */
            $primaryKey = Structure::getTablePrimaryKey($this);

            if ($primaryKey->autoIncrement) {
                $this->{$primaryKey->getPropertyField()} = $this->_id;
            }

            $this->_state = 1;
        } else {
            // Update
            $result = static::getLink()->update(Structure::getTable($this)->getFullTableName(), $this->getColumns(), $this->getPrimaryKey(),
                array_merge($this->getColumns(true), $this->getPrimaryKey(true)));
        }

        return $result;
    }


    /**
     * Delete from database.
     *
     * @return bool|int False if the current entity isn't saved, integer with affected rows when successfully deleted.
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     * @throws \Exception
     */
    public function delete()
    {
        if ($this->_state !== 1) {
            return false;
        }

        return static::getLink()->delete(Structure::getTable($this)->getFullTableName(), $this->getPrimaryKey(), $this->getPrimaryKey(true));
    }
}
