<?php namespace helpers;
/*
 * url Class
 *
 * @author David Carr - dave@daveismyname.com - http://www.daveismyname.com
 * @version 2.0
 * @date June 27, 2014
 */
class Url {

	/**
	 * Redirect to chosen url
	 * @param  string  $url      the url to redirect to
	 * @param  boolean $fullpath if true use only url in redirect instead of using DIR
	 */
	public static function redirect($url = null, $fullpath = false){
		
		if($fullpath == false){
			header('location: '.DIR.$url);
			exit;
		} else {
			header('location: '.$url);
			exit;
		}
		
	}

	/**
	 * created the absolute address to the template folder
	 * @return string url to template folder 
	 */
	public static function get_template_path(){
	    return DIR.'app/templates/'.Session::get('template').'/';
	}

	/**
	 * converts plain text urls into HTML links, second argument will be
	 * used as the url label <a href=''>$custom</a>
	 * 
	 * @param  string $text   data containing the text to read
	 * @param  string $custom if provided, this is used for the link label
	 * @return string         returns the data with links created around urls
	 */
	public static function autolink($text,$custom = null) {
		if($custom == null){
			return preg_replace('@(http)?(s)?(://)?(([-\w]+\.)+([^\s]+)+[^,.\s])@', '<a href="http$2://$4">$1$2$3$4</a>', $text);
		} else {
			return preg_replace('@(http)?(s)?(://)?(([-\w]+\.)+([^\s]+)+[^,.\s])@', '<a href="http$2://$4">'.$custom.'</a>', $text);
		}  
	} 
}