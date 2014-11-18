<?php namespace core;

/*
 * Language - simple language handler
 *
 * @author Bartek Kuśmierczuk - contact@qsma.pl - http://qsma.pl
 * @version 2.1
 * @date November 18, 2014
 */
class Language {

	/**
	 * Variable holds array with language
	 * @var array
	 */
	public $_array;

	/**
	 * 
	 */
	public function load($name, $code = LANGUAGE_CODE) {
		
		// lang file
		$file = "app/language/$code/$name.php";

		// check if is readable
		if(is_readable($file)){

			// require file
			$this->_array = include($file);

		} else {

			// display error
			echo \core\error::display("Could not load language file '$code/$name.php'");
			die;

		}

	}

	public function get($value) {
		return $this->_array[$value];
	}

}
