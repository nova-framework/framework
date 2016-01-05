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
     * Primary Key reference or value
     */
    private $_id = null;

    /**
     * Will be called each time a static call is made, to check if the entity is indexed
     *
     * @param $method
     * @param $parameters
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
        $this->_state = 0;
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
     * Set customized link name or instance
     *
     * @param string|null|Connection $link
     *
     * @return bool Successful or not
     */
    public static function setLink($link = null)
    {
        if ($link instanceof Connection) {
            self::$_link = $link;
            return true;
        }

        $linkInstance = DBALManager::getConnection($link);

        if ($linkInstance instanceof Connection) {
            self::$_link = $linkInstance;
            self::$_linkName = $link;
            return true;
        }

        return false;
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
     * @param string|int|array $id Primary key value or key=>value array for condition.
     * @return Entity|false
     *
     * @throws \Exception
     */
    public static function find($id)
    {
        $primaryKey = Structure::getTablePrimaryKey(self::$_table->name);

        if ($primaryKey === false) {
            throw new \Exception("Primary Key can't be detected!");
        }

        if (! is_array($id)) {
            $id = array($primaryKey->name => $id);
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

        /** @var Entity $result */
        $result = self::getLink()->fetchClass("SELECT * FROM " . self::$_table->prefix . self::$_table->name . " WHERE $where", $params, array(), get_called_class());

        if($result instanceof Entity) {
            $result->_state = 1;
        }

        return $result;
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
        $primaryKey = Structure::getTablePrimaryKey(self::$_table->name);

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
     * Insert or update the entity in the database
     *
     * @return int Affected rows
     * @throws \Exception Throws exceptions on error.
     */
    public function save()
    {
        if ($this->_state == 0) {
            // Insert
            $result = $this->getLink()->insert(self::$_table->prefix . self::$_table->name, $this->getColumns(), $this->getColumns(true));

            // Primary Key
            $this->_id = $this->getLink()->lastInsertId();

            /** @var Column $primaryKey */
            $primaryKey = Structure::getTablePrimaryKey($this);

            if ($primaryKey->autoIncrement) {
                $this->{$primaryKey->getPropertyField()} = $this->_id;
            }

            $this->_state = 1;
        } else {
            // Update
            $result = $this->getLink()->update(self::$_table->prefix . self::$_table->name, $this->getColumns(), $this->getPrimaryKey(),
                array_merge($this->getColumns(true), $this->getPrimaryKey(true)));
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

        return $this->getLink()->delete(self::$_table->prefix . self::$_table->name, $this->getPrimaryKey(), $this->getPrimaryKey(true));
    }
}
