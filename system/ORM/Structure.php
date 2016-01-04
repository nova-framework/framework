<?php
/**
 * Structure Manager
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @version 3.0
 * @date January 2nd, 2016
 */

namespace Nova\ORM;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Nova\ORM\Annotation\Column;
use Nova\ORM\Annotation\Table;

/**
 * Structure helper, will read and cache table and column structures by reading the Annotations.
 *
 * @package Nova\ORM
 */
abstract class Structure
{
    /** @var array */
    private static $columns = array();

    /** @var array */
    private static $tables = array();

    /**
     * Analyse entity and save table and column data for caching
     *
     * @param $instance Entity
     * @throws \Exception
     */
    public static function indexEntity($instance)
    {
        $reader = new AnnotationReader();
        $reflectionClass = new \ReflectionClass($instance);
        $className = $reflectionClass->getName();

        if (isset(self::$columns[$className], self::$tables[$className])) {
            // Already indexed!
            return;
        }

        /** @var Table $tableAnnotation */
        $tableAnnotation = $reader->getClassAnnotation($reflectionClass, "\\Nova\\ORM\\Annotation\\Table");
        if (! $tableAnnotation) {
            throw new AnnotationException("Table Annotation is not setup in your Entity!");
        }

        // Save table information
        self::$tables[$className] = $tableAnnotation;

        // Make column array
        self::$columns[$tableAnnotation->name] = array();

        // Get properties and loop.
        $properties = $reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC);

        foreach($properties as $prop) {
            /** @var Column $columnAnnotation */
            $columnAnnotation = $reader->getPropertyAnnotation($prop, "\\Nova\\ORM\\Annotation\\Column");

            // Add to columns
            if (! isset(self::$columns[$tableAnnotation->name][$columnAnnotation->name])) {
                self::$columns[$tableAnnotation->name][$columnAnnotation->name] = $columnAnnotation;
            }
        }
    }
}