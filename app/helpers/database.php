<?php namespace helpers;

use \PDO;

/*
 * database Helper - extending PDO to use custom methods
 *
 * @author David Carr - dave@daveismyname.com - http://www.daveismyname.com
 * @version 2.1
 * @date June 27, 2014
 */
class Database extends PDO{
	
	/**
	 * @var array Array of saved databases for reusing
	 */
	protected static $instances = array();
	
	/**
	 * Static method get 
	 * 
	 * @param  array $group
	 * @return \helpers\database
	 */
	public static function get ($group = false) {
		// Determining if exists or it's not empty, then use default group defined in config
		$group = !$group ? array (
			'type' => DB_TYPE,
			'host' => DB_HOST,
			'name' => DB_NAME,
			'user' => DB_USER,
			'pass' => DB_PASS
		) : $group;
		
		// Group information
		$type = $group['type'];
		$host = $group['host'];
		$name = $group['name'];
		$user = $group['user'];
		$pass = $group['pass'];
		
		// ID for database based on the group information
		$id = "$type.$host.$name.$user.$pass";
		
		// Checking if the same 
		if(isset(self::$instances[$id])) {
			return self::$instances[$id];
		}
		
		try {
			// I've run into problem where
			// SET NAMES "UTF8" not working on some hostings.
			// Specifiying charset in DSN fixes the charset problem perfectly!
			$instance = new Database("$type:host=$host;dbname=$name;charset=utf8", $user, $pass);
			$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			// Setting Database into $instances to avoid duplication
			self::$instances[$id] = $instance;
			
			return $instance;
		} catch(PDOException $e){
			//in the event of an error record the error to errorlog.html
			Logger::newMessage($e);
			Logger::customErrorMsg();
		}
	}

	/**
	 * method for selecting records from a database
	 * @param  string $sql       sql query
	 * @param  array  $array     named params
	 * @param  object $fetchMode
	 * @param  string $class     class name
	 * @return array            returns an array of records
	 */
	public function select($sql,$array = array(), $fetchMode = PDO::FETCH_OBJ, $class = ''){

		$stmt = $this->prepare($sql);
		foreach($array as $key => $value){
			if(is_int($value)){
				$stmt->bindValue("$key", $value, PDO::PARAM_INT); 
			} else {
				$stmt->bindValue("$key", $value); 
			}
		}

		$stmt->execute();

		if ($fetchMode === PDO::FETCH_CLASS) {
			return $stmt->fetchAll($fetchMode, $class);
		} else {
			return $stmt->fetchAll($fetchMode);
		}
	}

	/**
	 * insert method
	 * @param  string $table table name
	 * @param  array $data  array of columns and values
	 */
	public function insert($table, $data){

		ksort($data);

		$fieldNames = implode(',', array_keys($data));
		$fieldValues = ':'.implode(', :', array_keys($data));

		$stmt = $this->prepare("INSERT INTO $table ($fieldNames) VALUES ($fieldValues)");

		foreach($data as $key => $value){
			$stmt->bindValue(":$key", $value);
		}

		$stmt->execute();
		return $this->lastInsertId();

	}

	/**
	 * update method
	 * @param  string $table table name
	 * @param  array $data  array of columns and values
	 * @param  array $where array of columns and values
	 */
	public function update($table, $data, $where){
		
		ksort($data);

		$fieldDetails = NULL;
		foreach($data as $key => $value){
			$fieldDetails .= "$key = :$key,";
		}
		$fieldDetails = rtrim($fieldDetails, ',');

		$whereDetails = NULL;
		$i = 0;
		foreach($where as $key => $value){
			if($i == 0){
				$whereDetails .= "$key = :$key";
			} else {
				$whereDetails .= " AND $key = :$key";
			}
			
		$i++;}
		$whereDetails = ltrim($whereDetails, ' AND ');

		$stmt = $this->prepare("UPDATE $table SET $fieldDetails WHERE $whereDetails");

		foreach($data as $key => $value){
			$stmt->bindValue(":$key", $value);
		}

		foreach($where as $key => $value){
			$stmt->bindValue(":$key", $value);
		}

		$stmt->execute();

	}

	/**
	 * Delete method
	 * @param  string $table table name
	 * @param  array $data  array of columns and values
	 * @param  array $where array of columns and values
	 * @param  integer $limit limit number of records
	 */
	public function delete($table, $where, $limit = 1){

		ksort($where);

		$whereDetails = NULL;
		$i = 0;
		foreach($where as $key => $value){
			if($i == 0){
				$whereDetails .= "$key = :$key";
			} else {
				$whereDetails .= " AND $key = :$key";
			}
			
		$i++;}
		$whereDetails = ltrim($whereDetails, ' AND ');

		//if limit is a number use a limit on the query
		if(is_numeric($limit)){
			$uselimit = "LIMIT $limit";
		}

		$stmt = $this->prepare("DELETE FROM $table WHERE $whereDetails $uselimit");

		foreach($where as $key => $value){
			$stmt->bindValue(":$key", $value);
		}

		$stmt->execute();

	}

	/**
	 * truncate table
	 * @param  string $table table name
	 */
	public function truncate($table){
		return $this->exec("TRUNCATE TABLE $table");
	}

}
