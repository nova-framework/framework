<?php

class View {

	public function render($path,$data = false, $error = false){
		require "app/views/$path.php";
	}

	public function rendertemplate($path,$data = false){
		require "app/templates/".Session::get('template')."/$path.php";
	}
	
}