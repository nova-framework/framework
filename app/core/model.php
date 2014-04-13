<?php 

class Model {

	protected $_db;
	
	public function __construct(){
		//connect to PDO here.
		$this->_db = new Database();
	}
}
