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
 *
 * @template <T> Entity generics, type of the entity itself.
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
     * Prepare the where, return an array with the following details:
     *  'where' => 'SQL Where Clause'
     *  'bindValues' => array with values for binding.
     *  'bindTypes' => array with types for binding.
     *
     * @param array $criteria Should be either
     * 'column' => 'value'
     *          or:
     * 'column' => array('LIKE' => 'value')
     * 'column' => array('=' => 'value')
     * 'column' => array('>=' => 'value')
     * 'column' => array('<=' => 'value')
     * 'column' => array('>' => 'value')
     * 'column' => array('<' => 'value')
     * 'column' => array('IN' => array('value', 'value'))
     *
     * @param string|null $singleOperator Operator when doing a single criteria inline
     * @param string|null $singleValue Single or array value(s) when doing a single criteria inline
     *
     * @return array Where and bind result
     *
     * @throws \Exception Exceptions on error in the where criteria
     */
    private static function prepareWhere($criteria, $singleOperator = null, $singleValue = null)
    {
        // Check parameter
        if (! is_array($criteria) && ($singleOperator == null || $singleValue == null)) {
            return false;
        }

        // Prepare returning result
        $result = array(
            'where' => '',
            'bindValues' => array(),
            'bindTypes' => array()
        );

        // Map a single criteria to the normal array criteria.
        if (is_string($criteria) && $singleOperator !== null && $singleValue !== null) {
            $criteria = array($criteria => array($singleOperator => $singleValue));
        }

        // First we will loop through the criteria and prepare the where clause
        $where = "";
        $bindValues = array();
        $bindTypes = array();

        $idx = 0;
        foreach ($criteria as $column => $value) {
            // Check for operators, if no operator, add it explicit
            if (!is_array($value)) {
                $value = array('=' => $value);
            }

            // Will contain [operator] => value
            $operator = array_keys($value);

            // Few checks
            if (count($operator) !== 1) {
                throw new \Exception("The operator => value should contain only one operator, " . count($operator) . " operators given for column " . $column);
            }

            // Get the real operator and value
            $operator = $operator[0];
            $value = $value[$operator];

            // More checks for value
            if (is_array($value) && $operator !== 'IN') {
                throw new \Exception("Value is an array in the criteria of column " . $column . ". Only IN operator allows arrays given as criteria value!");
            }
            if ($operator == 'IN' && !is_array($value)) {
                throw new \Exception("Value should be an array of values criteria of column " . $column . ". Because you are using the IN operator!");
            }

            // Adding basic where
            $where .= "$column ";

            // The IN magic:
            if ($operator == 'IN' && is_array($value)) {
                $where .= " IN (";

                $subIdx = 0;

                foreach ($value as $item => $subValue) {
                    $where .= "?";
                    if (($subIdx + 1) !== count($value)) {
                        $where .= ",";
                    }

                    $bindValues[] = $subValue;
                    $bindTypes[] = is_int($subValue) ? PDO::PARAM_INT : PDO::PARAM_STR;

                    $subIdx++;
                }
                $where .= ")";
            } else {
                // None IN, just single where clause item.
                $where .= "$operator ?";
                $bindValues[] = $value;
                $bindTypes[] = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            }

            // If not the end of the criteria, then add AND to it.
            if (($idx + 1) !== count($criteria)) {
                $where .= " AND ";
            }


            $idx++;
        }

        $result['where'] = $where;
        $result['bindValues'] = $bindValues;
        $result['bindTypes'] = $bindTypes;

        return $result;
    }





    /**
     * Find entity by searching for the exact ID key.
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

            return $result;
        }

        return false;
    }


    /**
     * Find a single entity in database by searching for the given criteria.
     *
     * This will make a query and execute it, return an unique entity with entities or false/Exception on error
     *
     * @param array $criteria Array of key=>value where the key is the column name and the value is the required value
     * You could also use one of the custom comparators, like:
     *  - column => ['=' => value], column => ['<' => value], column => ['LIKE' => value] or column => ['>=' => value]
     *
     * When using a single criteria you could use this parameter as the column and the other 2 parameters as operator and value
     *
     *
     * @param null|string $operator Operator to use when having a single criteria
     * @param null|string $value Value (or multiple when using IN as a operator) to have a single criteria.
     *
     * @return Entity|Entity<T> Single entity
     * @throw \Exception Exceptions when having errors while preparing, fetching, connecting or parsing.
     */
    public static function findBy($criteria, $operator = null, $value = null)
    {
        if (! is_array($criteria) && ($operator == null || $value == null))
        {
            throw new \UnexpectedValueException("Criteria should be an array!");
        }

        // Prepare where statement
        $preparedWhere = self::prepareWhere($criteria, $operator, $value);

        $where = $preparedWhere['where'];

        $sql = "SELECT * FROM " . Structure::getTable(static::class)->getFullTableName() . " WHERE $where";
        $result = self::getLink()->fetchClass($sql, $preparedWhere['bindValues'], $preparedWhere['bindTypes'], static::class);

        if($result instanceof Entity) {
            $result->_state = 1;

            return $result;
        }

        return false;
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
