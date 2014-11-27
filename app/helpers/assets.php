<?php namespace helpers;

/**
 * Assets static helper
 * 
 * @author volter9
 * @date 27th November, 2014
 */

class Assets {
	
	/**
	 * Add script to the template
	 * 
	 * @param string $file
	 */
	public static function script ($file) {
		echo '<script src="' . $file . '" type="text/javascript"></script>';
	}
	
	/**
	 * Add stylesheet to the template
	 * 
	 * @param string $file
	 */
	public static function stylesheet ($file) {
		echo '<link href="' . $file . '" rel="stylesheet" type="text/css"/>';
	}
	
	/**
	 * Add several scripts to the template
	 * 
	 * @param arrat $files
	 */
	public static function scripts (array $files) {
		if (count($files) > 0) {
			foreach ($files as $file) {
				self::script($file);
			}
		}
	}
	
	/**
	 * Add several stylesheets to the template
	 * 
	 * @param array $files
	 */
	public static function stylesheets (array $files) {
		if (count($files) > 0) {
			foreach ($files as $file) {
				self::stylesheet($file);
			}
		}
	}
	
}