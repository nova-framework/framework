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
use Nova\ORM\Entity;

/**
 * Structure helper, will read and cache table and column structures by reading the Annotations.
 *
 * @package Nova\ORM
 */
abstract class Structure
{
    /** @var array */
    private static $columns = array();

    /** @var Table[] */
    private static $tables = array();

    /**
     * Analyse entity and save table and column data for caching
     *
     * @param Entity|string $instance
     * @return Table
     * @throws \Exception
     */
    public static function indexEntity($instance)
    {
        $reader = new AnnotationReader();
        $reflectionClass = new \ReflectionClass($instance);
        $className = $reflectionClass->getName();

        if (isset(self::$columns[$className], self::$tables[$className])) {
            // Already indexed!
            return self::$tables[$className];
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

            if ($columnAnnotation instanceof Column) {
                // Add to columns
                if (! isset(self::$columns[$tableAnnotation->name][$columnAnnotation->name])) {
                    self::$columns[$tableAnnotation->name][$columnAnnotation->name] = $columnAnnotation;
                }
            }
        }

        return $tableAnnotation;
    }


    /**
     * Get table columns from entity or table name.
     * @param string|Entity $table Entity or tablename
     * @return bool|Column[]
     */
    public static function getTableColumns($table)
    {
        if ($table instanceof Entity) {
            $reflectionClass = new \ReflectionClass($table);
            $className = $reflectionClass->getName();

            // Get table name from entity
            if (! isset(self::$tables[$className])) {
                return false;
            }

            $table = self::$tables[$className]->name;
        }

        if (!is_string($table)) {
            throw new \UnexpectedValueException("Table parameter should be a name of the table, or an instance of the Entity!");
        }

        if (isset(self::$columns[$table])) {
            return self::$columns[$table];
        }

        return false;
    }


    /**
     * Get primary key columns for table.
     * @param string|Entity $table Entity or tablename
     * @return bool|Column[]
     */
    public static function getTablePrimaryKeys($table)
    {
        if ($table instanceof Entity) {
            $reflectionClass = new \ReflectionClass($table);
            $className = $reflectionClass->getName();

            // Get table name from entity
            if (! isset(self::$tables[$className])) {
                return false;
            }

            $table = self::$tables[$className]->name;
        }

        if (!is_string($table)) {
            throw new \UnexpectedValueException("Table parameter should be a name of the table, or an instance of the Entity!");
        }

        if (! isset(self::$columns[$table])) {
            return false;
        }

        // Check for all primary keys
        $primaryKeys = array();
        foreach(self::$columns[$table] as $column) {
            if ($column->primary) {
                $primaryKeys[] = $column;
            }
        }

        return $primaryKeys;
    }
}