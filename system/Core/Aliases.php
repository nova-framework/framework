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
 * Aliases - make alias for classes for Views to use without declaring a use element.
 */
class Aliases
{
    public static function init()
    {
        $classes = Config::get('classAliases');

        if(! is_array($classes)) {
            return;
        }

        foreach ($classes as $classAlias => $className) {
            // This ensures the alias is created in the global namespace.
            $classAlias = '\\' .ltrim($classAlias, '\\');

            // Check if the Class already exists.
            if (class_exists($classAlias)) {
                // Bail out, a Class already exists with the same name.
                throw new RuntimeException('A class ('.$classAlias.') already exists with the same name!');
            }

            class_alias($className, $classAlias);
        }
    }
}
