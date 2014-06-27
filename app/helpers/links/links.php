<?php namespace helpers\links;

/*
*
* Adapted from http://stackoverflow.com/a/3305795
*/

class Links {

	public static function autolink($text,$custom = null) {
		if($custom == null){
			return preg_replace('@(http)?(s)?(://)?(([-\w]+\.)+([^\s]+)+[^,.\s])@', '<a href="http$2://$4">$1$2$3$4</a>', $text);
		} else {
			return preg_replace('@(http)?(s)?(://)?(([-\w]+\.)+([^\s]+)+[^,.\s])@', '<a href="http$2://$4">'.$custom.'</a>', $text);
		}
	    
	}

}