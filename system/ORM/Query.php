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


    /**
     * Check if given operator is a valid operator.
     * @param string $operator
     * @return bool
     */
    private function validOperator($operator)
    {
        $valid = array("=", "!=", "LIKE", ">", "<", ">=", "<=", "IN", "<>");
        return in_array($operator, $valid, true);
    }

    /**
     * Validate value for given operator
     * @param mixed $value
     * @param string $operator
     * @return bool
     */
    private function validValue($value, $operator)
    {
        if (! $this->validOperator($operator)) {
            return false;
        }

        if ($operator === "IN") {
            // Valid should be an array!
            return is_array($value);
        }

        return !is_array($value);
    }
}