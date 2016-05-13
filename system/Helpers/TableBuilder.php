<?php
/**
 * Table builder class.
 *
 * @author Volter9 - https://github.com/Volter9
 * @version 3.0
 */

namespace Helpers;

use PDO;
use Helpers\Database;

/**
 * Table builder class for the Nova Framework.
 * This class' purpose is to generate SQL code and execute queries
 * to create a MySQL table.
 *
 * For 'CREATE TABLE' syntax reference visit: http://dev.mysql.com/doc/refman/5.1/en/create-table.html [1]
 *
 * Example of usage:
 *
 * // After namespace: use Helpers\TableBuilder;
 *
 * // Model or Controller method
 * $tableBuilder = new tableBuilder;
 *
 * $tableBuilder->addField('name', 'string', false);
 * $tableBuilder->addField('description', 'description', false);
 * $tableBuilder->addField('date', 'TIMESTAMP', false, tableBuilder::CURRENT_TIMESTAMP);
 * $tableBuilder->addField('online', 'TINYINT(1)', false);
 *
 * $tableBuilder->setDefault('online', 0);
 * $tableBuilder->setName('comments');
 * $tableBuilder->setNotExists(true);
 *
 * $tableBuilder->create();
 *
 * @author volter9
 * @copyright volter9 ( c ) 2014
 */
class TableBuilder
{
    /**
     * @const int AUTO_INCREMENT    AUTO_INCREMENT flat
     * @const int CURRENT_TIMESTAMP Default type CURRENT_TIMESTAMP
     */
    const AUTO_INCREMENT = 1;
    const CURRENT_TIMESTAMP = 2;

    /**
     * @var Helpers\Database A database instance
     */
    protected $db;

    /**
     * @var string Compiled SQL query
     */
    private $sql = '';

    /**
     * Name
     *
     * @var string $name   Table name
     */
    private $name = '';

    /**
     * Fields
     *
     * @var array  $fields Table fields
     */
    private $fields = array();

    /**
     * Primary Key
     *
     * @var strin  $pk     Primary key field
     */
    private $pk = '';

    /**
     * @var boolean Prevents any errors in case the table already exists
     */
    private $notExists = false;

    /**
     * @var array Type aliases
     */
    private static $typeAliases = array (
        'int'         => 'INT(11)',
        'string'      => 'VARCHAR(255)',
        'description' => 'TINYTEXT'
    );

    /**
     * Set alias.
     * An alias is just a way to simplify datatypes of field in expression.
     * You probably don't want to write a lot of times INT(11), so you can add 'int' alias 'INT(11)'
     *
     * @param string $aliasName - Name of the Alias
     * @param string $aliasType - Type of the Alias
     */
    public static function setAlias($aliasName, $aliasType)
    {
        self::$typeAliases[$aliasName] = $aliasType;
    }

    /**
     * Table builder constructor.
     * Database class initialization, don't create too many instances of table builder,
     * because it will create many database instances which will decrease server performance.
     * By default this class would create an `id` field INT(11) NOT null AUTO_INCREMENT PRIMARY KEY, unless
     * you set the second parameter as false.
     *
     * @param PDO|null $db - PDO instance (it can be a Helper\Database instance)
     * @param boolean  $id - A flag to add or not to add an `id` field automatically
     */
    public function __construct(PDO $db = null, $id = true)
    {
        // If the database is not given, create a new database instance.
        // If the database is in the same namespace, we don't need to specify namespace
        $this->db = !$db ? Database::get() : $db;

        if ($id === true) {
            $this->addField('id', 'INT(11)', false, self::AUTO_INCREMENT);
            $this->setPK('id');
        }
    }

    /**
     * Private utility for converting constants into strings.
     *
     * @param int|array $constant - Constant(s) to convert
     * @return string
     */
    private function getOptions($constant)
    {
        if (is_array($constant)) {
            $str = '';

            foreach ($constant as $value) {
                $str .= $this->getOptions($value);
            }

            return trim($str);
        }

        switch ($constant) {
            case self::AUTO_INCREMENT:
                return 'AUTO_INCREMENT';

            default:
                return '';
        }
    }

    /**
     * Add a field to the table definition.
     *
     * @param string    $field   - Field name
     * @param string    $type    - Type of the field, for types, please visit CREATE TABLE page for reference
     * @param boolean   $null    - NOT null or null
     * @param array|int $options - Options, it's either an array of constants or just one constant
     */
    public function addField($field, $type, $null = false, $options = 0)
    {
        // Check for an alias.
        if (isset(self::$typeAliases[$type])) {
            $type = self::$typeAliases[$type];
        }

        $this->fields[$field] = array (
            'type'    => $type,
            'null'    => $null,
            'options' => $options
        );

        if ($options === self::CURRENT_TIMESTAMP ||
            is_array($options) &&
            in_array(self::CURRENT_TIMESTAMP, $options)) {
            $this->fields[$field]['default'] = 'CURRENT_TIMESTAMP';
        }
    }

    /**
     * Set the 'IF NOT EXISTS' property.
     *
     * @param boolean $boolean
     */
    public function setNotExists($boolean)
    {
        $this->notExists = $boolean;
    }

    /**
     * Set the Primary Key field.
     *
     * @param string $field - Field which you want to set a primary key
     * @return boolean
     */
    public function setPK($field)
    {
        if (!isset($this->fields[$field])) {
            return false;
        }

        $this->pk = $field;

        return true;
    }

    /**
     * Set the name of a table.
     *
     * @param string $name - A name for the database table
     */
    public function setName($name)
    {
        if (is_string($name) && $name !== '') {
            $this->name = $name;
        }
    }

    /**
     * Add default field to a specific field.
     * Note: to add CURRENT_TIMESTAMP, use the addField method and the $options argument!
     *
     * @param string $field - Field that needs default value
     * @param mixed  $value - A value that you want to add
     */
    public function setDefault($field, $value)
    {
        if (is_string($value)) {
            $value = "'$value'";
        }

        $this->fields[$field]['default'] = $value;
    }

    /**
     * Procedure for generating SQL code from input data.
     * The most nasty part of the class, procedural generation of query.
     */
    public function generateSQL()
    {
        $sql = 'CREATE TABLE ';

        if ($this->notExists) {
            $sql = $sql . 'IF NOT EXISTS ';
        }

        $sql .= "{$this->name} (";

        // Handle fields
        foreach ($this->fields as $name => $field) {
            $sql .= "`$name` {$field['type']} " . ($field['null'] === false ? 'NOT' : '') . " null ";

            if (isset($field['default'])) {
                $sql .= "DEFAULT {$field['default']} ";
            }

            $sql .= $this->getOptions($field['options']) . ', ';
        }

        if ($this->pk !== '') {
            $sql .= "CONSTRAINT pk_{$this->pk} PRIMARY KEY (`{$this->pk}`)";
        }

        // Remove additional commas
        $sql = rtrim($sql, ', ') . ')';

        $this->sql = $sql;
    }

    /**
     * Retrieve SQL, if you might need it.
     *
     * @return string
     */
    public function getSQL()
    {
        if (!$this->sql) {
            $this->generateSQL();
        }

        return $this->sql;
    }

    /**
     * Create a table.
     *
     * @param boolean $reset - A flag to reset data.
     * @return boolean
     */
    public function create($reset = true)
    {
        if (!$this->sql) {
            $this->generateSQL();
        }

        $result = $this->db->exec($this->sql);

        if ($reset) {
            $this->reset();
        }

        return $result !== false;
    }

    /**
     * Reset the properties of tableBuilder class so
     * you could build another table.
     */
    public function reset()
    {
        $this->sql = '';
        $this->name = '';
        $this->pk = '';
        $this->notExists = false;

        $this->fields = array();
    }
}
