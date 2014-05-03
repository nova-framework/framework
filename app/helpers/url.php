<?php

class Url {

	public static function redirect($url = null, $fullpath = false){

		if($fullpath == false){
			header('location: '.DIR.$url);
			exit;
		} else {
			header('location: '.$url);
			exit;
		}

	}

	public static function get_template_path(){
	    return DIR.'app/templates/'.Session::get('template').'/';
	}
}
