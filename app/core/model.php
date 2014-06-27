<?php namespace core;

class Model extends Controller {

	protected $_db;
	
	public function __construct(){
		//connect to PDO here.
		$this->_db = new \helpers\database();

	}
}
