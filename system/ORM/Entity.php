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
    private $_state = 0;

    /**
     * Link name for using in this entity
     *
     * @var string
     */
    private static $_linkName = 'default';

    /**
     * Link instance (DBAL instance) used for this entity.
     *
     * @var null|Connection
     */
    private static $_link = null;

    /**
     * Indexed table annotation data
     *
     * @var Annotation\Table|null
     */
    private static $_table = null;

    /**
     * Binding Primary Key
     */
    private $_id = null;

    /**
     * Will be called each time a static call is made, to check if the entity is indexed
     *
     * @param $method
     * @param $parameters
     */
    public static function __callStatic($method, $parameters){
        echo __CLASS__ . "::" . $method;
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
        if (!self::$_table) {
            // Index entity.
            self::$_table = Structure::indexEntity(get_called_class());
            self::$_linkName = self::$_table->link;
        }
        self::$_link = null;
        self::$_linkName = "";
    }


    public function __construct()
    {
        self::discoverEntity();
    }


    /**
     * Get Link instance
     *
     * @return Connection
     */
    private static function getLink()
    {
        if (self::$_link == null) {

            if (self::$_linkName == '') {
                self::$_linkName = 'default';
            }

            self::$_link = DBALManager::getConnection(self::$_linkName);
        }
        return self::$_link;
    }


    /**
     * Query Builder for finding
     *
     * @return QueryBuilder
     */
    public static function getQueryBuilder()
    {
        return self::getLink()->createQueryBuilder();
    }


    /**
     * Get from database with primary key value.
     *
     * @param string|int|array $id Primary key value(s). If there are multiple primary keys, give an array with
     * all the values!
     * @return Entity|false
     *
     * @throws \Exception
     */
    public static function get($id)
    {
        $primaryKeys = Structure::getTablePrimaryKeys(self::$_table->name);

        if ($primaryKeys === false) {
            throw new \Exception("Primary Keys can't be detected!");
        }

        if (! is_array($id) && count($primaryKeys) > 1) {
            throw new \UnexpectedValueException("ID parameter should be an array with primary key -> values. The current entity has multiple primary keys!");
        }

        if (is_array($id) && count($primaryKeys) !== count($id)) {
            throw new \OutOfBoundsException("The ID array should contain all primary keys defined in your entity.");
        }

        if (! is_array($id)) {
            $id = array($primaryKeys[0]->name => $id);
        }


        $where = "";
        $params = array();
        foreach($id as $key => $value) {
            $where .= "$key = ?";
            $params[] = $value;

            if (count($params) !== count($id)) {
                $where .= " AND ";
            }
        }

        return self::getLink()->fetchClass("SELECT * FROM " . self::$_table->prefix . self::$_table->name . " WHERE $where", $params, array(), get_called_class());
    }


    /**
     * Get Entity properties as assoc array. useful for insert, update or just debugging.
     *
     * @param bool $types Get types of columns. Default false.
     * @return array
     */
    public function getColumnArray($types = false)
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
    public function getPrimaryArray($types = false)
    {
        $primaryKeys = Structure::getTablePrimaryKeys(self::$_table->name);

        if ($primaryKeys === false) {
            throw new \Exception("Primary Keys can't be detected!");
        }

        $data = array();
        foreach($primaryKeys as $column) {
            if ($types) {
                $data[$column->name] = $column->getPdoType();
            } else {
                $data[$column->name] = $this->{$column->getPropertyField()};
            }
        }

        return $data;
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
            $result = $this->getLink()->insert(self::$_table->prefix . self::$_table->name, $this->getColumnArray());

            // Primary Key
            $this->_id = $this->getLink()->lastInsertId();

            /** @var Column[] $primaryKeys */
            $primaryKeys = Structure::getTablePrimaryKeys($this);

            foreach($primaryKeys as $primaryKey) {
                if ($primaryKey->autoIncredimental) {
                    $this->{$primaryKey->getPropertyField()} = $this->_id;
                }
            }

            $this->_state = 1;
        } else {
            // Update
            $result = $this->getLink()->update(self::$_table->prefix . self::$_table->name, $this->getColumnArray(), $this->getPrimaryArray());
        }

        return $result;
    }


    /**
     * Delete from database
     *
     * @return bool|int False if the current entity isn't saved, integer with affected rows when successfully deleted.
     *
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     * @throws \Exception
     */
    public function delete()
    {
        if ($this->_state !== 1) {
            return false;
        }

        return $this->getLink()->delete(self::$_table->prefix . self::$_table->name, $this->getPrimaryArray());
    }
}
