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
    /** @var array<string, Column[]> */
    private static $columns = array();

    /** @var array<string, Table> */
    private static $tables = array();

    /** @var array<string, Column> */
    private static $primaryKey = array();

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

        $tableAnnotation->setClassName($className);

        // Save table information
        self::$tables[$className] = $tableAnnotation;

        // Make column array
        self::$columns[$className] = array();

        // Get properties and loop.
        $properties = $reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $prop) {
            /** @var Column $columnAnnotation */
            $columnAnnotation = $reader->getPropertyAnnotation($prop, "\\Nova\\ORM\\Annotation\\Column");

            if ($columnAnnotation instanceof Column) {
                // Set field name
                $columnAnnotation->setPropertyField($prop->getName());

                // Add to columns
                if (! isset(self::$columns[$className][$columnAnnotation->name])) {
                    self::$columns[$className][$columnAnnotation->name] = $columnAnnotation;
                }

                // Add if primary to the pk array.
                if ($columnAnnotation->primary) {
                    self::$primaryKey[$className] = $columnAnnotation;
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
        } else {
            $className = $table;
        }

        if (! isset(self::$tables[$className]) || ! isset(self::$columns[$className])) {
            throw new \UnexpectedValueException("Table isn't in our structure index!");
        }

        return self::$columns[$className];
    }


    /**
     * Get primary key column for table
     * @param string|Entity $table Entity or table name
     * @return false|Column
     */
    public static function getTablePrimaryKey($table)
    {
        if ($table instanceof Entity) {
            $reflectionClass = new \ReflectionClass($table);
            $className = $reflectionClass->getName();
        } else {
            $className = $table;
        }

        if (! isset(self::$tables[$className]) || ! isset(self::$primaryKey[$className])) {
            throw new \UnexpectedValueException("Table name or instance isn't in our structure index!");
        }

        return self::$primaryKey[$className];
    }

    /**
     * Get table of current Entity class name (full class name)
     * @param string|Entity $class Class name or instance
     * @return Table|null
     */
    public static function getTable($class)
    {
        if ($class instanceof Entity) {
            $reflectionClass = new \ReflectionClass($class);
            $className = $reflectionClass->getName();
        } else {
            $className = $class;
        }

        if (isset(self::$tables[$className])) {
            return self::$tables[$className];
        }
    }
}
