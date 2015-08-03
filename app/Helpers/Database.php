<?php
namespace Helpers;

use PDO;

/*
 * database Helper - extending PDO to use custom methods
 *
 * @author David Carr - dave@simplemvcframework.com
 * @version 2.1
 * @date June 27, 2014
 * @date May 18 2015
 */
class Database extends PDO
{
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
    public static function get($group = false)
    {
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
        if (isset(self::$instances[$id])) {
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
        } catch (PDOException $e) {
            //in the event of an error record the error to ErrorLog.html
            Logger::newMessage($e);
            Logger::customErrorMsg();
        }
    }

    /**
     * run raw sql queries
     * @param  string $sql sql command
     * @return none
     */
    public function raw($sql)
    {
        $this->query($sql);
    }

    /**
  	 * Metodo para executar sqls (create, update, delete)
  	 * @param string $sql
  	 * @return int id
  	 */

  	public function execute($sql) {
  			$stmt = $this->prepare($sql);
  			return ($stmt->execute ()) ? $this->lastInsertId() : false;
  	}

  	/**
  	 * Metodo para contar registros de uma consulta
  	 * @param string $sql
  	 * @return int total de registros
  	 */

  	public function rowCount($sql){
  		$stmt = $this->prepare($sql);
  		return ($stmt->execute ()) ? $stmt->rowCount () : false;
  	}

  	/**
  	 * Busca dados em uma tabela especifica
  	 * @param  string $table     nome da tabela
  	 * @param  array  $array     parametros nomeados
  	 * @param  object $fetchMode
  	 * @return array            return array com os dados obtidos
  	 */

  	public function find($table, $data = array(), $mode = "fetchAll", $fetchMode = PDO::FETCH_ASSOC){

  		$sql =  "SELECT * FROM $table ";

  		if($data){

  			$where = $this->where($data);
  			$sql .= $where['where'];
  			$data = $where['data'];

  			return $this->select($sql, $data, $mode, $fetchMode);
  		}

  		else{
  			return $this->select($sql, array(), $mode, $fetchMode);
  		}

  	}

  	/**
  	 * Monta as condições de consulta de acordo os parametros passados no array
  	 * @param  array  $array     parametros nomeados
  	 * @return array  return array com os dois indices
  	 *
  	 *		$array['where'] ref string condicoes. Ex. WHERE id = :id AND name = :name
  	 * 		$array['data'] ref array com os dados para filtragem do PDO. Ex. array(':id' => valor);
  	 */
  	public function where($data){
  			$where = "";
  			$array = array();
  			foreach ($data as $key => $value){

  				$field = str_replace('-', '.', $key);
  				$value = ':'.str_replace('-', '_', $key);

  				$array[$field] = $value;
  				$where .= "$field = $value AND ";
  			}

  			$where = " WHERE " . rtrim($where, ' AND ');

  			$keys = array_values($array);
  			$values = array_values($data);
  			$data = array_combine($keys, $values);

  			return array('where' => $where, 'data' => $data);
  	}

  	/**
  	 * Metodo de seleção de registros
  	 * @param  string $sql       sql query
  	 * @param  array  $array     parametros nomeados
  	 * @param  object $fetchMode
  	 * @return array            return array com os dados obtidos
  	 */

  	public function select($sql, $array = array(), $mode = "fetchAll", $fetchMode = PDO::FETCH_ASSOC){

  		$stmt = $this->prepare($sql);
  		foreach($array as $key => $value){
  			if(is_int($value)){
  				$stmt->bindValue("$key", $value, PDO::PARAM_INT);
  			} else {
  				$stmt->bindValue("$key", $value);
  			}
  		}

  		$stmt->execute();
  		if($mode == "fetchAll"){
  			return $stmt->fetchAll($fetchMode);
  		}else if($mode == "fetch"){
  			return $stmt->fetch($fetchMode);
  		}

  	}

  	/**
  	 * Metodo para inserir registros
  	 * @param  string $table nome da tabela
  	 * @param  array $data  arry com colunas e valores
  	 */

  	public function insert($table, $data){

  		$data[DATETIME_INSERT] = date('Y-m-d H:i:s');
  		$data = $this->cleanFieldsTable($table, $data);

  		ksort($data);

  		$fieldNames = implode(',', array_keys($data));
  		$fieldValues = ':'.implode(', :', array_keys($data));

  		$stmt = $this->prepare("INSERT INTO $table ($fieldNames) VALUES ($fieldValues)");

  		foreach($data as $key => $value){

  			if(is_int($value)){
  				$stmt->bindValue(":$key", $value, PDO::PARAM_INT);

  			} else if(is_string($value)){
  				$stmt->bindValue(":$key", $value, PDO::PARAM_STR);

  			} else{
  				$stmt->bindValue(":$key", $value);
  			}
  		}

  		return ($stmt->execute()) ? $this->lastInsertId() : false;

  	}

  	/**
  	 * Metodo para atualizar registros
  	 * @param  string $table nome da tabela
  	 * @param  array $data  arry com colunas e valores
  	 * @param  array $where arry com colunas e valores
  	 */

  	public function update($table, $data, $where){

  		$data[DATETIME_UPDATE] = date('Y-m-d H:i:s');
  		$data = $this->cleanFieldsTable($table, $data);

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

  			if(is_int($value)){
  				$stmt->bindValue(":$key", $value, PDO::PARAM_INT);

  			} else if(is_string($value)){
  				$stmt->bindValue(":$key", $value, PDO::PARAM_STR);

  			} else{
  				$stmt->bindValue(":$key", $value);
  			}
  		}

  		foreach($where as $key => $value){
  			if(is_int($value)){
  				$stmt->bindValue(":$key", $value, PDO::PARAM_INT);

  			} else if(is_string($value)){
  				$stmt->bindValue(":$key", $value, PDO::PARAM_STR);

  			} else{
  				$stmt->bindValue(":$key", $value);
  			}
  		}

  		return $stmt->execute();

  	}

  	/**
  	 * Metodo para deletar registros
  	 * @param  string $table nome da tabela
  	 * @param  array $data  arry com colunas e valores
  	 * @param  array $where arry com colunas e valores
  	 * @param  integer $limit limit number of records
  	 */

  	public function delete($table, $where, $limit = null){

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
  		$uselimit = ($limit) ? "LIMIT $limit" : "";

  		$stmt = $this->prepare("DELETE FROM $table WHERE $whereDetails $uselimit");

  		foreach($where as $key => $value){
  			$stmt->bindValue(":$key", $value);
  		}

  		return $stmt->execute();

  	}

  	/**
  	 * truncate table
  	 * @param  string $table nome da tabela
  	 */

  	public function truncate($table){
  		return $this->exec("TRUNCATE TABLE $table");
  	}

  	/**
  	 * Lista todos os campos de uma tabela
  	 * @param  string $table nome da tabela
  	 * @return array resultado obtido
  	 */

  	public function fieldsTable($table){
  		//RETORNA UM ARRAY COM TODOS OS CAMPOS DE UMA TABELA
  		$results = $this->select("SHOW COLUMNS FROM {$table}");

  		$fields_table = array();
  		foreach ($results as $fields){
  			$fields_table[] = $fields['Field'];
  		}
  		return $fields_table;
  	}

  	/**
  	 * Limpa array deixando apenas os regitros com indices/campos iguais na tabela
  	 * @param string $table nome da tabela
  	 * @param array Arry com colunas e valores
  	 * @return array Arry limpo com colunas e valores
  	 */

  	public function cleanFieldsTable($table, $data){

  		//Verifica a diferenca entre os arrays
  		$diferencas = array_diff(array_keys($data), $this->fieldsTable($table));

  		//Exclui os indices não encontrados na tabela
  		foreach ($diferencas as $key => $value){
  			unset($data[$value]);
  		}

  		return $data;

  	}
}
