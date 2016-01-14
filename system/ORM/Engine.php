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


abstract class Engine
{
    /*
     * The used \Nova\Database\Connection instance.
     */
    protected $db = null;

    /*
     * Internal static Cache.
     */
    protected static $cache = array();

    /*
     * There is stored the called Class name.
     */
    protected $className;

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

            $tableName = Inflector::pluralize(basename($classPath));

            $this->tableName = Inflector::tableize($tableName);
        }

        // Get the Table Fields.
        /* The Table Fields should be specified into form:
        array(
            'first_field' => 'string'
            'other_field' => 'int'
            'third_field' => 'string'
        );
        */

        if(! empty($this->fields)) {
            // Do nothing.
        }
        else if ($this->getCache('$tableFields$') === null) {
            $fields = $this->db->getTableFields($this->table());

            foreach($fields as $field => $fieldInfo) {
                $this->fields[$field] = $fieldInfo['type'];
            }

            $this->setCache('$tableFields$', $this->fields);
        } else {
            $this->fields = $this->getCache('$tableFields$');
        }
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
    // Caching Management Methods
    //--------------------------------------------------------------------

    protected function getCache($name)
    {
        $token = $this->className .'_' .$name;

        if (isset(self::$cache[$token])) {
            return self::$cache[$token];
        }

        return null;
    }

    protected function setCache($name, $value)
    {
        $token = $this->className .'_' .$name;

        self::$cache[$token] = $value;
    }

    protected function clearCache($name)
    {
        $token = $this->className .'_' .$name;

        if (isset(self::$cache[$token])) {
            unset(self::$cache[$token]);
        }
    }

    protected function hasCached($name)
    {
        $token = $this->className .'_' .$name;

        return isset(self::$cache[$token]);
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
    // Debug Methods
    //--------------------------------------------------------------------

    public function lastSqlQuery()
    {
        return $this->db->lastSqlQuery();
    }

}
