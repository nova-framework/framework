<?php namespace helpers;

/*
 * Table builder class
 *
 * @author Volter9 - https://github.com/Volter9
 * @version 2.1
 * @date Sept 22, 2014
 */

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
 * $tableBuilder->addField('name', 'string', FALSE);
 * $tableBuilder->addField('description', 'description', FALSE);
 * $tableBuilder->addField('date', 'TIMESTAMP', FALSE, tableBuilder::CURRENT_TIMESTAMP);
 * $tableBuilder->addField('online', 'TINYINT(1)', FALSE);
 * 
 * $tableBuilder->setDefault('online', 0);
 * $tableBuilder->setName('comments');
 * $tableBuilder->setNotExists(TRUE);
 * 
 * $tableBuilder->create();
 * 
 * @author volter9
 * @copyright volter9 ( c ) 2014
 */

class tableBuilder {
	
	/**
	 * @const int AUTO_INCREMENT    AUTO_INCREMENT flat
	 * @const int CURRENT_TIMESTAMP Default type CURRENT_TIMESTAMP
	 */
	const AUTO_INCREMENT = 1;
	const CURRENT_TIMESTAMP = 2;
	
	/**
	 * @var \helpers\database A database instance
	 */
	protected $_db;
	
	/**
	 * @var string Compiled SQL query
	 */
	private $_sql = '';
	
	/**
	 * @var string $_name   Table name
	 * @var array  $_fields Table fields
	 * @var strin  $_pk     Primary key field
	 */
	private $_name = '';
	private $_fields = array();
	private $_pk = '';
	
	/**
	 * @var boolean Prevents errors in case if table already exists
	 */
	private $_notExists = FALSE;
	
	/**
	 * @var array Type aliases
	 */
	private static $_typeAliases = array (
		'int'         => 'INT(11)',
		'string'      => 'VARCHAR(255)',
		'description' => 'TINYTEXT'
	);
	
	/**
	 * Set alias.
	 * Alias is just a way to simplify datatype of field in expression.
	 * You probably don't want to write a lot of times INT(11), so you can add 'int' alias 'INT(11)'
	 * 
	 * @param string $aliasName - Name of the Alias
	 * @param string $aliasType - Type of the Alias
	 */
	public static function setAlias ($aliasName, $aliasType) {
		self::$_typeAliases[$aliasName] = $aliasType;
	}
	
	/**
	 * Table builder constructor.
	 * Database class initialization, don't create too many instances of table builder,
	 * because it will create many database instances which will decrease performance.
	 * By default this class would create a `id` field INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, unless
	 * you'll set second parameter FALSE.
	 * 
	 * @param PDO|null $db - PDO instance (it can be a \helper\database instance)
	 * @param boolean  $id - A flag to add or not to add `id` field automatically
	 */
	public function __construct (PDO $db = NULL, $id = TRUE) {
		// If database is not given, create new database instance.
		// database is in the same namespace, we don't need to specify namespace
		$this->_db = !$db ? database::get() : $db;
		
		if ($id === TRUE) {
			$this->addField('id', 'INT(11)', FALSE, self::AUTO_INCREMENT);
			$this->setPK('id');
		}
	}
	
	/**
	 * Private utility for converting constants into strings.
	 * 
	 * @param int|array $constant - Constant(s) to convert
	 * @return string
	 */
	private function getOptions ($constant) {
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
	 * @param boolean   $null    - NOT NULL or NULL
	 * @param array|int $options - Options, it's either array of constants or just one constant
	 */
	public function addField ($field, $type, $null = FALSE, $options = 0) {
		// Check for alias
		if ( isset(self::$_typeAliases[$type]) ) {
			$type = self::$_typeAliases[$type]; 
		}
		
		$this->_fields[$field] = array (
			'type'    => $type,
			'null'    => $null,
			'options' => $options
		);
		
		if ($options === self::CURRENT_TIMESTAMP ||
			is_array($options) &&
			in_array(self::CURRENT_TIMESTAMP, $options)) {
			$this->_fields[$field]['default'] = 'CURRENT_TIMESTAMP';
		}
	}
	
	/**
	 * Sets 'IF NOT EXISTS' property
	 * 
	 * @param boolean $boolean
	 */
	public function setNotExists ($boolean) {
		$this->_notExists = $boolean;
	}
	
	/**
	 * Set Primary Key field
	 * 
	 * @param string $field - Field which you want to set a primary key
	 * @return boolean
	 */
	public function setPK ($field) {
		if ( !isset($this->_fields[$field]) ) {
			return FALSE;
		}
		
		$this->_pk = $field;
		
		return TRUE;
	}
	
	/**
	 * Set name of table
	 * 
	 * @param string $name - A name for database
	 */
	public function setName ($name) {
		if (is_string($name) && $name !== '') {
			$this->_name = $name;
		}
	}
	
	/**
	 * Adding default field for specific field.
	 * Note: to add CURRENT_TIMESTAMP, use addField method and $options argument!
	 * 
	 * @param string $field - Field that need default value
	 * @param mixed  $value - Value that you want to add
	 */
	public function setDefault ($field, $value) {
		if (is_string($value)) {
			$value = "'$value'";
		}
		
		$this->_fields[$field]['default'] = $value;
	}
	
	/**
	 * Procedure for generating SQL code from input data.
	 * The most nasty part of the class, procedural generation of query.
	 */
	public function generateSQL () {
		$sql = 'CREATE TABLE ';
		
		if ($this->_notExists) {
			$sql = $sql . 'IF NOT EXISTS ';
		}
		
		$sql .= "{$this->_name} (";
		
		// Handling fields
		foreach ($this->_fields as $name => $field) {
			$sql .= "`$name` {$field['type']} " . ($field['null'] === FALSE ? 'NOT' : '') . " NULL ";
			
			if (isset($field['default'])) {
				$sql .= "DEFAULT {$field['default']} ";
			}
			
			$sql .= $this->getOptions($field['options']) . ', ';
		}
		
		if ($this->_pk !== '') {
			$sql .= "CONSTRAINT pk_{$this->_pk} PRIMARY KEY (`{$this->_pk}`)";
		}
		
		// Removing additional commas
		$sql = rtrim($sql, ', ') . ')';
		
		$this->_sql = $sql;
	}
	
	/**
	 * Get SQL, if you might need it.
	 * 
	 * @return string
	 */
	public function getSQL () {
		if (!$this->_sql) {
			$this->generateSQL();
		}
		
		return $this->_sql;
	}
	
	/**
	 * Creates table
	 * 
	 * @param boolean $reset - A flag to reset whole set of data.
	 * @return boolean
	 */
	public function create ($reset = TRUE) {
		if (!$this->_sql) {
			$this->generateSQL();
		}
		
		$result = $this->_db->exec($this->_sql);
		
		if ($reset) {
			$this->reset();
		}
		
		return $result !== FALSE;
	}
	
	/**
	 * Resets the properties of tableBuilder class so 
	 * you could build another table.
	 */
	public function reset () {
		$this->_sql = '';
		$this->_name = '';
		$this->_pk = '';
		$this->_notExists = FALSE;
		
		$this->_fields = array();
	}
	
}
