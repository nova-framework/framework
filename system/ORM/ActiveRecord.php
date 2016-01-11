<?php
/**
 * ActiveRecord
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 11th, 2016
 */

namespace Nova\ORM;

use Nova\Helpers\Inflector;
use Nova\ORM\Expects;
use Nova\ORM\Connection\Wrapper as ConnectionWrapper;


class ActiveRecord extends ConnectionWrapper
{
    protected $isNew = true;

    protected $db;
    protected $adapter;

    protected static $cache = array();

    protected $primaryKey = 'id';

    protected $tableName;
    protected $serialize;

    public $belongsTo = array();
    public $hasOne    = array();
    public $hasMany   = array();


    public function __construct()
    {
        $className = get_class($this);

        if (empty($this->tableName)) {
            $tableName = Inflector::pluralize($className);

            $this->tableName = Inflector::tableize($tableName);
        }

        $this->belongsTo = Expects::toAssocArray($this->belongsTo);
        $this->hasOne    = Expects::toAssocArray((array)$this->hasOne);
        $this->hasMany   = Expects::toAssocArray((array)$this->hasMany);

        $this->serialize = Expects::toArray($this->serialize);

        // Get the Table Fields.
        if ($this->getCache('$tableFields$') === null) {
            $this->fields = $this->getTableFields();

            $this->setCache('$tableFields$', $this->fields);
        } else {
            $this->fields = $this->getCache('$tableFields$');
        }

        // Get the number of arguments.
        $numArgs = func_num_args();

        // Setup the Object according with its arguments.
        if ($numArgs == 1) {
            $arg = func_get_arg(0);

            if (is_array($arg)) {
                $this->initWithArray($arg);
            } else {
                $this->initFromId($arg);
            }
        } else if ($numArgs == 0) {
            $this->isNew = true;
        } else {
            throw new \Exception('Invalid number of arguments to initialization of ' .$className);
        }
    }

    private function initObject($isNew)
    {
        $this->isNew = $isNew;

        if (! $this->isNew) {
            $this->unserializeFields();
        }

        $this->afterLoad();
    }

    private function unserializeFields()
    {
        foreach ((array)$this->serialize as $field) {
            if (! empty($this->$field)) {
                $this->$field = unserialize($this->$field);
            }
        }
    }

    private function serializeFields()
    {
        foreach ((array) $this->serialize as $field) {
            if (! empty($this->$field)) {
                $this->$field = serialize($this->$field);
            }
        }
    }

    private function initWithArray($assocArray)
    {
        $this->initFromAssocArray($assocArray);

        $this->initObject(true);
    }

    private function initFromId($id)
    {
        // TBD

        $this->initObject(false);
    }

    private function initFromAssocArray(array $assocArray)
    {
        foreach ($assocArray as $key => $value) {
            $this->$key = $value;
        }
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    private function getCache($name)
    {
        $token = get_class($this) .'_' .$name;

        if (isset(self::$cache[$token])) {
            return self::$cache[$token];
        }

        return null;
    }

    private function setCache($name, $value)
    {
        $token = get_class($this) .'_' .$name;

        self::$cache[$token] = $value;
    }

    public function __get($name) {

        if ($this->getCache($name) !== null) {
            return $this->getCache($name);
        }

        if (isset($this->belongsTo[$name])) {
            $value = $this->belongsTo[$name];

            if (strpos($value, ':') !== false) {
                list($key, $className) = explode(':', $value);
            } else {
                $key = $name . '_id';

                $className = $value;
            }

            if (isset($this->$key) && ! empty($this->$key)) {
                $obj = new $className($this->$key);

                $this->setCache($name, $obj);

                return $obj;
            }
        }

        if (isset($this->hasOne[$name])) {
            $value = $this->hasOne[$name];

            if (strpos($value, ':') !== false) {
                list($key, $className) = explode(':', $value);
            } else {
                $fieldName = Inflector::singularize($this->tableName);

                $key = $fieldName .'_id';

                $className = $value;
            }

            $obj = new $className();

            $result = $obj->findBy($key, $this->{$this->primaryKey});

            $this->setCache($name, $result);

            return $result;
        }

        if (isset($this->hasMany[$name])) {
            $value = $this->hasMany[$name];

            if (strpos($value, ':') !== false) {
                list($key, $className) = explode(':', $value);
            } else {
                $fieldName = Inflector::singularize($this->tableName);

                $key = $fieldName . '_id';

                $className = $value;
            }

            $obj = new $className();

            $result = $obj->findBy($key, $this->{$this->primaryKey});

            $this->setCache($name, $result);

            return $result;
        }
    }

    public function setAttributes($attributes)
    {
        $this->initWithAssocArray($attributes);
    }

    public function getAttributes()
    {
        $result = array();

        foreach ((array) $this->fields as $key => $value) {
            $result[$key] = $this->$key;
        }

        return $result;
    }

    public function __toString()
    {
        $result = '';

        // Support for checking if an object is empty.

        if ($this->isNew) {
            $isEmpty = true;

            foreach ($this->fields as $fieldName => $fieldInfo) {
                if (! empty($this->$fieldName)) {
                    $isEmpty = false;

                    break;
                }
            }

            if ($isEmpty) {
                return $result; // NOTE: there the result is an empty string.
            }
        }

        $result = get_class($this) ."(" .$this->{$this->primaryKey} .")\n";

        foreach ($this->fields as $fieldName => $fieldInfo) {
            $result .= "\t" .ucfirst($fieldName) .': ' .$this->$fieldName ."\n";
        }

        foreach ($this->hasOne as $fieldName => $className) {
            $result .= "\t" .ucfirst($fieldName) .": (reference to a $className object)\n";
        }

        foreach ($this->hasMany as $fieldName => $className) {
            $result .= "\t" .ucfirst($fieldName) .": (reference to $className objects)\n";
        }

        foreach ($this->belongsTo as $fieldName => $className) {
            $result .= "\t" .ucfirst($fieldName) .": (reference to a $className object)\n";
        }

        return $result;
    }

    //
    // Overwritable methods.

    public function beforeSave()
    {
        return true;
    }

    public function afterLoad()
    {
        return true;
    }

    public function beforeDestroy()
    {
        return true;
    }

}
