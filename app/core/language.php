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
	private $_array;

	/**
	 * Load language function
	 * @param  string $name
	 * @param  string $code
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

	/**
	 * Get element from language array by key
	 * @param  string $value
	 * @return string
	 */
	public function get($value) {

		if(!empty($this->_array[$value])){
			return $this->_array[$value];
		} else {
			return $value;
		}

	}

	/**
	 * Get lang for views
	 * @param  string $value this is "word" value from language file
	 * @param  string $name  name of file with language
	 * @param  string $code  optional, language code
	 * @return string
	 */
	public function show($value, $name, $code = LANGUAGE_CODE) {
		
		// lang file
		$file = "app/language/$code/$name.php";

		// check if is readable
		if(is_readable($file)){

			// require file
			$_array = include($file);

		} else {

			// display error
			echo \core\error::display("Could not load language file '$code/$name.php'");
			die;

		}

		// If 
		if(!empty($_array[$value])){
			return $_array[$value];
		} else {
			return $value;
		}
	}

}
