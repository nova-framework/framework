<?php namespace helpers;

/**
* Password - simple password helper
* 
* @author Bartek Kuśmierczuk - contact@qsma.pl - http://qsma.pl
* @version 1.0
* @date 2015-04-21 13:24:18
*/
class Password {

	/**
	 * Generate random string for salt
	 * @param  integer $length
	 * @return string
	 */
	public function generateString($length = 32) {

		// String variable
		$string = null;

		// Generate string
		$string = bin2hex(mcrypt_create_iv($length, MCRYPT_DEV_URANDOM));

		// Check if not null
		if ($string == null) {
			trigger_error('Password not generated, variable string is empty', E_USER_WARNING);
			die;
		}

		// Return generated string
		return $string;
	}

	/**
	 * Make password method
	 * @param  string  $password this variable get password from user
	 * @param  integer $cost     cost of salt
	 * @return string            this is generated password
	 */
	public function make($password, $salt = null, $cost = 12) {

		// Check cost value
		if ($cost < 4 || $cost > 31) {
			trigger_error('Password cost must be bigger then 4 and lower then 31', E_USER_WARNING);
			die;
		}

		// Check salt
		if ($salt == null) {

			// Create format
			$salt_format = "$2y$%u$%s";

			// Init salt generator
			$random_salt = self::generateString();

			// Generate salt
			$salt = sprintf($salt_format, $cost, $random_salt);
		}

		// Generate password
		$hash = crypt($password, $salt);

		// Return password
		return $hash;
	}

	/**
	 * Verify password
	 * @param  string $password current user password
	 * @param  string $hash 	password for example from db
	 * @return bool 			if true password is verify else false
	 */
	public function verify($password = null, $hash = null) {

		// Checking if the password is correct
		if (self::make($password, $hash) == $hash) {
			return true;
		} else {
			return false;
		}

	}

}
