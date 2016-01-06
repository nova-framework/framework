<?php
/**
 * ORM Entity Query
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @version 3.0
 * @date January 6th, 2015
 */

namespace Nova\ORM;

/**
 * Class Query, Building a Query for finding entities.
 *
 * @package Nova\ORM
 */
class Query
{
    /** @var null|string Entity Class Name (full, with namespace) */
    private $entityClass = null;

    // Holding the query parts:
    private $where = array();
    private $limit = null;
    private $offset = null;


    /**
     * Query constructor.
     *
     * @param string $entityClass Class name (full) of the fetching entity
     */
    public function __construct($entityClass)
    {
        $class = new \ReflectionClass($entityClass);

        if (! $class->isSubclassOf("\\Nova\\ORM\\Entity")) {
            throw new \UnexpectedValueException("The entity class should extend the \\Nova\\ORM\\Entity class!");
        }

        $this->entityClass = $class->getName();
    }
}