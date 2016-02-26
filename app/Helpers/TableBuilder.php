<?php
/**
 * Table builder class.
 *
 * @author Volter9 - https://github.com/Volter9
 *
 * @version 2.2
 * @date Sept 22, 2014
 * @date updated Sept 19, 2015
 */
namespace Helpers;

use PDO;

/**
 * Table builder class for SimpleMVCFramework.
 * This class' purpose is to generate SQL code and execute query
 * to create MySQL table.
 *
 * For 'CREATE TABLE' syntax reference visit: http://dev.mysql.com/doc/refman/5.1/en/create-table.html [1]
 *
 * Example of usage:
 *
 * // After namespace: use \helpers\tableBuilder;
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
     * @var \helpers\database A database instance
     */
    protected $db;

    /**
     * @var string Compiled SQL query
     */
    private $sql = '';

    /**
     * Name.
     *
     * @var string Table name
     */
    private $name = '';

    /**
     * fields.
     *
     * @var array Table fields
     */
    private $fields = [];

    /**
     * pk.
     *
     * @var strin Primary key field
     */
    private $pk = '';

    /**
     * @var bool Prevents errors in case if table already exists
     */
    private $notExists = false;

    /**
     * @var array Type aliases
     */
    private static $typeAliases =  [
        'int'         => 'INT(11)',
        'string'      => 'VARCHAR(255)',
        'description' => 'TINYTEXT',
    ];

    /**
     * Set alias.
     * Alias is just a way to simplify datatype of field in expression.
     * You probably don't want to write a lot of times INT(11), so you can add 'int' alias 'INT(11)'.
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
     * because it will create many database instances which will decrease performance.
     * By default this class would create a `id` field INT(11) NOT null AUTO_INCREMENT PRIMARY KEY, unless
     * you'll set second parameter false.
     *
     * @param PDO|null $db - PDO instance (it can be a \helper\database instance)
     * @param bool     $id - A flag to add or not to add `id` field automatically
     */
    public function __construct(PDO $db = null, $id = true)
    {
        // If database is not given, create new database instance.
        // database is in the same namespace, we don't need to specify namespace
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
     *
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
     * Add a field to table definition.
     *
     * @param string    $field   - Field name
     * @param string    $type    - Type of the field, for types, please visit CREATE TABLE page for reference
     * @param bool      $null    - NOT null or null
     * @param array|int $options - Options, it's either array of constants or just one constant
     */
    public function addField($field, $type, $null = false, $options = 0)
    {
        // Check for alias
        if (isset(self::$typeAliases[$type])) {
            $type = self::$typeAliases[$type];
        }

        $this->fields[$field] =  [
            'type'    => $type,
            'null'    => $null,
            'options' => $options,
        ];

        if ($options === self::CURRENT_TIMESTAMP ||
            is_array($options) &&
            in_array(self::CURRENT_TIMESTAMP, $options)) {
            $this->fields[$field]['default'] = 'CURRENT_TIMESTAMP';
        }
    }

    /**
     * Sets 'IF NOT EXISTS' property.
     *
     * @param bool $boolean
     */
    public function setNotExists($boolean)
    {
        $this->notExists = $boolean;
    }

    /**
     * Set Primary Key field.
     *
     * @param string $field - Field which you want to set a primary key
     *
     * @return bool
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
     * Set name of table.
     *
     * @param string $name - A name for database
     */
    public function setName($name)
    {
        if (is_string($name) && $name !== '') {
            $this->name = $name;
        }
    }

    /**
     * Adding default field for specific field.
     * Note: to add CURRENT_TIMESTAMP, use addField method and $options argument!
     *
     * @param string $field - Field that need default value
     * @param mixed  $value - Value that you want to add
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
            $sql = $sql.'IF NOT EXISTS ';
        }

        $sql .= "{$this->name} (";

        // Handling fields
        foreach ($this->fields as $name => $field) {
            $sql .= "`$name` {$field['type']} ".($field['null'] === false ? 'NOT' : '').' null ';

            if (isset($field['default'])) {
                $sql .= "DEFAULT {$field['default']} ";
            }

            $sql .= $this->getOptions($field['options']).', ';
        }

        if ($this->pk !== '') {
            $sql .= "CONSTRAINT pk_{$this->pk} PRIMARY KEY (`{$this->pk}`)";
        }

        // Removing additional commas
        $sql = rtrim($sql, ', ').')';

        $this->sql = $sql;
    }

    /**
     * Get SQL, if you might need it.
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
     * Creates table.
     *
     * @param bool $reset - A flag to reset whole set of data.
     *
     * @return bool
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
     * Resets the properties of tableBuilder class so
     * you could build another table.
     */
    public function reset()
    {
        $this->sql = '';
        $this->name = '';
        $this->pk = '';
        $this->notExists = false;

        $this->fields = [];
    }
}
