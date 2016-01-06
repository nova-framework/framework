<?php
/**
 * ORM Entity Query
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @version 3.0
 * @date January 6th, 2015
 */

namespace Nova\ORM;
use Nova\ORM\Annotation\Column;

/**
 * Class Query, Building a Query for finding entities.
 *
 * @package Nova\ORM
 */
class Query
{
    /** @var null|string Entity Class Name (full, with namespace) */
    private $entityClass = null;

    /** @var array<string, Column>|null */
    private $columnData = null;

    // Holding the query parts:
    private $where = array();

    private $limit = null;
    private $offset = null;

    private $orderBy = null;
    private $orderType = null;


    // Error stack
    private $lastException = null;


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

        // Get column data and index it with column name in the key. Used for validating later on.
        $this->columnData = array();
        $columns = Structure::getTableColumns($this->entityClass);
        foreach($columns as $column) {
            $this->columnData[$column->name] = $column;
        }
    }

    /**
     * Add where conditions, you can give the conditions in multiple styles:
     * 1. -> where('id', 1)                    for id= 1 condition
     * 2. -> where('id', '=', 1)               for id = 1 condition
     * 3. -> where('id', 'IN', array(1, 2))    for id IN (1,2) condition
     * 4. -> where(array('id' => 1))           same as first style
     * 5. -> where(array('id' => array('=' => 1)) same as second style
     *
     * @param string|array $criteria String with column name, or array with condition for full where syntax
     *
     * @param string|null $operator Only when using column name in first parameter, fill this by the value when comparing
     * or fill in the operator used to compare
     *
     * @param string|null $value Only when using column name in first parameter and filling in the operator value.
     *
     * @return Query $this Return chained query.
     */
    public function where($criteria, $operator = null, $value = null)
    {
        // If the operator is the value, then we are going to use the = operator
        if (! is_array($criteria) && $value === null && $this->validValue($operator, "=")) {
            // Operator is now value!
            $criteria = array($criteria => array("=" => $operator));
        }

        // If it's the shorthand of the where, convert it to the normal criteria.
        if (! is_array($criteria) && $this->validOperator($operator) && $this->validValue($value, $operator)) {
            $criteria = array($criteria => array($operator => $value));
        }

        // Get column names of table
        $columns = Structure::getTableColumns($this->entityClass);

        // Parse criteria, validate and add to the current where clause.
        foreach ($criteria as $column => $compare) {
            // If using shorthand for = compare
            if (! is_array($compare)) {
                $criteria[$column] = array('=' => $compare);
            }

            // Validate compare, validate column name
            if (! isset($this->columnData[$column])) {
                $this->lastException = new \Exception("Trying to prepare a where with column condition for a undefined column!", 0, $this->lastException);
                continue;
            }

            $operator = array_keys($compare);
            $operator = $operator[0];
            $value = $compare[$operator];

            if ($this->validOperator($operator) && $this->validValue($value, $operator)) {
                // Add to the Query Where stack
                $this->where[] = array(
                    'column' => $column,
                    'operator' => $operator,
                    'value' => $value
                );
            }
            // Skip if not valid.
        }

        var_dump($this->where);
        return $this;
    }


    /**
     * Limit the result
     *
     * @param int $limit Give the number of limited entities returned.
     * @return Query $this The current query stack.
     */
    public function limit($limit)
    {
        if (! is_int($limit) || $limit < 0) {
            $this->lastException = new \UnexpectedValueException("Limit value should be an positive integer!", 0, $this->lastException);
            return $this;
        }
        $this->limit = intval($limit);

        return $this;
    }

    /**
     * Offset the results
     *
     * @param int $offset Give the number of offset applied to the results.
     * @return Query $this The current query stack.
     */
    public function offset($offset)
    {
        if (! is_int($offset) || $offset < 0) {
            $this->lastException = new \UnexpectedValueException("Offset value should be an positive integer!", 0, $this->lastException);
            return $this;
        }
        $this->offset = intval($offset);

        return $this;
    }


    /**
     * Order by column value, Ascending or descending
     * @param string $column Column name to order with.
     * @param string $type Either ASC or DESC for the order type.
     * @return Query $this The current query stack.
     */
    public function order($column, $type = 'ASC')
    {
        // First lets upper the type.
        $type = strtoupper($type);

        // Validate the column
        if (isset($this->columnData[$column])) {
            $this->orderBy = $column;
            $this->orderType = $type;
        }

        return $this;
    }




    
    public function all()
    {

    }

    public function one()
    {

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

    /**
     * Validate type of ordering columns
     * @param string $type
     * @return bool
     */
    private function validOrderType($type)
    {
        return strtolower($type) === 'asc' || strtolower($type) === 'desc';
    }
}