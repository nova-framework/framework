<?php
/**
 * Alias - make helpers available in views
 *
 * @author David Carr - dave@novaframework.com
 * @version 3.0
 */
namespace Core;

use Core\Config;

/**
 * Aliases - make alias for classes for views to use without declaring a use element.
 */
class Aliases
{
    public static function init()
    {
        $classes = Config::get('class_aliases');

        if(is_array($classes)) {
            foreach ($classes as $classAlias => $className) {
                class_alias($className, $classAlias);
            }
        }
    }
}
