<?php namespace core;

/*
 * View - load template pages
 *
 * @author David Carr - dave@daveismyname.com - http://www.daveismyname.com
 * @version 2.1
 * @date June 27, 2014
 */
class View {

	/**
	 * include template file
	 * @param  string  $path  path to file from views folder
	 * @param  array $data  array of data
	 * @param  array $error array of errors
	 */
	public static function render($path,$data = false, $error = false){
		require "app/views/$path.php";
	}

	/**
	 * return absolute path to selected template directory
	 * @param  string  $path  path to file from views folder
	 * @param  array $data  array of data
	 */
	public static function rendertemplate($path,$data = false){
		require "app/templates/". \helpers\Session::get('template') ."/$path.php";
	}
	
}