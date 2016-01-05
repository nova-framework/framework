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
 * Class Entity, can be extended with your database entities
 */
abstract class Entity
{
    /**
     * Hold the state of the current Entity. Will be used to determinate if INSERT or UPDATE is needed
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
     * Will be called each time a static call is made, to check if the entity is indexed
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
     * Get Link instance
     *
     * @return Connection
     */
    private static function getLink()
    {
        return DBALManager::getConnection();
    }


    /**
     * Query Builder for finding
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
     * Get Entity properties as assoc array. useful for insert, update or just debugging.
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
     * Get Primary Key data array or type array
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
     * Get from database with primary key value.
     *
     * @param string|int $id Primary key value
     * @return Entity|false
     *
     * @throws \Exception
     */
    public static function find($id)
    {
        $primaryKey = Structure::getTablePrimaryKey(static::class);

        if ($primaryKey === false) {
            throw new \Exception("Primary Key can't be detected!");
        }
        // Only get column name
        $primaryKey = $primaryKey->name;

        /** @var Entity $result */
        $result = self::getLink()->fetchClass("SELECT * FROM " . Structure::getTable(static::class)->getFullTableName() . " WHERE $primaryKey = :pkvalue", array(':pkvalue' => $id), array(), static::class);

        if($result instanceof Entity) {
            $result->_state = 1;
        }

        return $result;
    }


    /**
     * Insert or update the entity in the database
     *
     * @return int Affected rows
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
     * Delete from database
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
