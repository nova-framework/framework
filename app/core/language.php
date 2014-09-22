<?php namespace core;
use core\error as Error;

/**
 * language - the language handler
 *
 * @author Edwin Hoksberg - info@edwinhoksberg.nl
 * @version 2.1
 * @date September 22, 2014
 */
class Language {

	/**
	 * The variable that holds all the language data
	 *
	 * @var array
	 */
	private $language_data = array();

	/**
	 * This function will load any language file, and optionally return the language data
	 *
	 * @param string $controller
	 * @param bool   $return
	 * @param string $language_code
	 *
	 * @return array|bool
	 */
	public function load($controller, $return = false, $language_code = null) {

		if ($language_code != null) {
			$language_file = 'app/language/' . $language_code . '/' . $controller . '.php';
		} else {
			$language_file = 'app/language/' . LANGUAGE_CODE . '/' . $controller . '.php';
		}

		if (is_readable($language_file)) {
			require_once($language_file);
			$this->language_data = array_merge($this->language_data, $lang);

			return ($return) ? $this->language_data : true;
		} else {
			Error::display('Could not load language file `' . $controller . '`');
			return false;
		}
	}

	/**
	 * This function will return an language string if it exists, else it will return false
	 *
	 * @param string $line
	 *
	 * @return string|bool
	 */
	public function get($line) {
		return (!empty($this->language_data[$line])) ? $this->language_data[$line] : false;
	}

}
