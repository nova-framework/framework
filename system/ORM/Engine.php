<?php
/**
 * Engine
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 14th, 2016
 */

namespace Nova\ORM;

use Nova\Helpers\Inflector;
use Nova\Database\Connection;
use Nova\Database\Manager as Database;
use Nova\Cache\Manager as CacheManager;

use \PDO;


abstract class Engine
{
    /*
     * The used \Nova\Database\Connection instance.
     */
    protected $db = null;

    /*
     * The used \Nova\Cache\Manager instance.
     */
    protected $cache;

    /*
     * Internal static Cache.
     */
    protected static $globalCache = array();

    /*
     * There is stored the called Class name.
     */
    protected $className;

    /**
     * The Table's Primary Key.
     */
    protected $primaryKey = 'id';

    /**
     * The Table Metadata.
     */
    protected $fields = array();

    /**
     * There we store the Model Attributes (its Data).
     */
    protected $attributes = array();

    /**
     * The Table name belonging to this Model.
     */
    protected $tableName;

    /*
     * Constructor
     */
    public function __construct($connection = 'default')
    {
        $this->className = get_class($this);

        if($connection instanceof Connection) {
            $this->db = $connection;
        } else {
            $this->db = Database::getConnection($connection);
        }

        // Setup the Table name, if is empty.
        if (empty($this->tableName)) {
            // Try the best to guess the Table name: User -> users
            $classPath = str_replace('\\', '/', $this->className);

            $name = Inflector::pluralize(basename($classPath));

            $this->tableName = Inflector::tableize($name);
        }

        // Setup the Cache instance.
        $this->cache = CacheManager::getCache();

        // Get the Table Fields.
        if(! empty($this->fields)) {
            // The user considered to directly specify the Table metadata.
            return;
        }

        // Prepare the Cache Token.
        $token = 'orm_table_fields_' .md5($this->tableName);

        // Try to get the Table Fields from the fast static Cache.
        if(isset(self::$globalCache[$token])) {
            $this->fields = self::$globalCache[$token];

            // The data was found in the static Cache.
            return;
        }

        // Get the Table Fields, using the Framework Caching.
        $fields = $this->cache->get($token);

        if($fields === null) {
             // No data found into Cache, then we should get it from Database.
            $fields = $this->db->getTableFields($this->table());

            foreach($fields as $field => $fieldInfo) {
                $this->fields[$field] = $fieldInfo['type'];
            }

            // Write to cache 300 seconds = 5 minutes
            $this->cache->set($token, $fields, 300);
        }

        // Store the data also into the fast static Cache.
        self::$globalCache[$token] = $fields;

        // Finaly, setup properly the Table Fields.
        $this->fields = $fields;
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Getter for the table name.
     *
     * @return string The name of the table used by this class (including the DB_PREFIX).
     */
    public function table()
    {
        return DB_PREFIX .$this->tableName;
    }

    //--------------------------------------------------------------------
    // Attributes handling Methods
    //--------------------------------------------------------------------

    public function setAttributes($attributes)
    {
        $this->hydrate($attributes);
    }

    public function attributes()
    {
        return $this->attributes;
    }

    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function attribute($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    public function getPrimaryKey()
    {
        if($this->isNew) {
            return null;
        }

        $key =& $this->primaryKey;

        if(isset($this->attributes[$key]) && ! empty($this->attributes[$key])) {
            return $this->attributes[$key];
        }

        return null;
    }

    //--------------------------------------------------------------------
    // Data Conversion Methods
    //--------------------------------------------------------------------

    public function toArray()
    {
        return $this->attributes;
    }

    public function toObject()
    {
        $object = new stdClass();

        foreach ($this->attributes as $key => $value) {
            $object->$key = $value;
        }

        return $object;
    }

    //--------------------------------------------------------------------
    // Select Methods
    //--------------------------------------------------------------------

    /**
     * Execute Select Query, binding values into the $sql Query.
     *
     * @param string $sql
     * @param array $bindParams
     * @param bool $fetchAll Ask the method to fetch all the records or not.
     * @return array|null
     *
     * @throws \Exception
     */
    public function select($sql, $params = array(), $fetchAll = false)
    {
        // Firstly, simplify the white spaces and trim the SQL query.
        $sql = preg_replace('/\s+/', ' ', trim($sql));

        // Prepare the parameter Types.
        $paramTypes = $this->getParamTypes($params);

        return $this->db->select($sql, $params, $paramTypes, 'array', $fetchAll);
    }

    //--------------------------------------------------------------------
    // Internal use Methods
    //--------------------------------------------------------------------

    protected function getParamTypes($params, $strict = true)
    {
        $fields =& $this->fields;

        $result = array();

        foreach($params as $field => $value) {
            if(isset($fields[$field])) {
                $fieldType = $fields[$field];

                $result[$field] = ($fieldType == 'int') ? PDO::PARAM_INT : PDO::PARAM_STR;
            }
            // No registered field found? We try to guess then the Type, if we aren't into strict mode.
            else if(! $strict) {
                $result[$field] = is_integer($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            }
        }

        return $result;
    }

    //--------------------------------------------------------------------
    // Debug Methods
    //--------------------------------------------------------------------

    public function lastSqlQuery()
    {
        return $this->db->lastSqlQuery();
    }

}
