<?php
/**
 * ORM Annotations - Table
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @version 3.0
 * @date January 2nd, 2016
 */

namespace Nova\ORM\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class Table
 * @package Nova\ORM\Annotation
 *
 * @Annotation
 * @Annotation\Target("CLASS")
 */
class Table extends Annotation
{
    /**
     * @var string
     * @Annotation\Required()
     */
    public $name;

    /**
     * @var string
     */
    public $prefix = DB_PREFIX;


    /**
     * Class Name of entity
     * @var null|string
     */
    private $className = null;

    /**
     * Get full table name
     * @return string
     */
    public function getFullTableName()
    {
        return $this->prefix . $this->name;
    }

    /**
     * Set class name, will be used by our structure indexer
     * @param $className
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }

    /**
     * Get the entity class, used with this table.
     * @return null|string
     */
    public function getClassName()
    {
        return $this->className;
    }
}
