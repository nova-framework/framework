<?php namespace helpers;

class Twig {

    public static function render ($path, $data = False, $extension ="tpl") {
		$loader_path = dirname($path); 

        $template_name = end(explode("/",$path)).".".$extension;
		
		$loader = new \Twig_Loader_Filesystem("app/views/$loader_path");
		$twig = new \Twig_Environment($loader, array(
                        'cache' =>  'cache',
                        'debug' =>  true,
                  'auto_reload' =>  true,
             'strict_variables' =>  true,
                   'autoescape' =>  true,

        ));

		echo $twig->render($template_name, $data);
    }

}