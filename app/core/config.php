<?php namespace core;

/*
 * config - setup system wide settings
 *
 * @author David Carr - dave@daveismyname.com - http://www.daveismyname.com
 * @version 2.1
 * @date June 27, 2014
 */
 
 /**
  * Config class.
  * 
  * Using this class you can create your own version of configs by specifying 
  * filepath to the *.ini file that you want to parse.
  *
  * This class is also gives you access to 
  *
  * @todo different types of configs (*.php, from database, etc.)
  * @todo config caching (APC, Memcached)
  */
class Config {
	
	// Constants
	const MAIN_CONFIG = 'app/config/config.ini';
	
	// Main config
	private static $_instance = NULL;
	
	/**
	 * Get the main config instance.
	 * Use this static method to gain access to the main config.
	 * 
	 * @return \core\config
	 */
	public static function getConfig () {
		if (self::$_instance === NULL) {
			// Starting output buffering
			ob_start();
			
			// Defining main config
			$config = new config;
			
			// Custom section of config file to constants
			$config->defineAsConstants('custom');
			
			// Default intialziation:
			
			// Turn on custom error handling
			set_exception_handler('core\logger::exception_handler');
			set_error_handler('core\logger::error_handler');
			
			// Set timezone
			date_default_timezone_set($config->get('general', 'timezone'));
			
			// Start sessions
			\helpers\session::init();
	
			// Set the default template
			\helpers\session::set('template', $config->get('general', 'template'));
			
			self::$_instance = $config;
		}
		
		return self::$_instance;
	}
	
	// Instance stuff
	protected $_config;
	
	public function __construct($file = self::MAIN_CONFIG) {
		if ( !file_exists($file) ) {
			throw new Exception ("Config cannot be initialized due to the missing file $file!");
		}
		
		$this->_config = parse_ini_file($file, TRUE);
	}
	
	/**
	 * Returns section or key from one section
	 * 
	 * @param string $section  - Specific section of the config file
	 * @param string $key = '' - Specific key of given section
	 * @return mixed
	 */
	public function get ($section = '', $key = '') {
		// Get the whole section
		if (!$section) {
			return $this->_config;
		}
		
		if (empty($key) && isset($this->_config[$section])) {
			return $this->_config[$section];
		}
		
		// Get only one key
		if ( isset($this->_config[$section][$key]) ) {
			return $this->_config[$section][$key];
		}
		
		return FALSE;
		
	}
	
	/**
	 * Define config section as constants.
	 * Pass a name of the section to "inject" config section into global scope using constants.
	 * 
	 * @param string $section - Given section to turn it into constants
	 * @return boolean
	 */
	public function defineAsConstants ($section = '') {
		if (!$section) {
			return;
		}
		
		$section = $this->get($section);
		
		// Verifying that section isn't empty
		if (empty($section)) {
			return;
		}
		
		// Creating constants out of section
		foreach ($section as $key => $value) {
			if (!is_array($value)) {
				$key = strtoupper($key);
				
				define($key, $value);
			}
		}
		
		return TRUE;
	}

}