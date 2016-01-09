<?php
/**
 * ORM Entity Query
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @version 3.0
 * @date January 6th, 2015
 */

namespace Nova\ORM;

use Nova\ORM\Database;
use Nova\ORM\Annotation\Column;
use PDO;

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


    /**
     * The query that will be build.
     *
     * @var string
     */
    private $query = "";

    /**
     * The where part of the query
     *
     * @var string
     */
    private $whereClause = "";

    /**
     * The where part of the query, binding values
     * @var array
     */
    private $whereBindValues = array();

    /**
     * The where part of the query, binding types
     * @var array
     */
    private $whereBindTypes = array();



    /**
     * Error Exception
     * @var null|\Exception|\Throwable
     */
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
                $compare = array('=' => $compare);
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


    /**
     * Execute Query and fetch all records as entities
     *
     * @return Entity[]|false Entities as successful result or false on not found.
     * @throws \Exception|\Throwable
     */
    public function all()
    {
        // Build query
        $this->buildQuery();

        return $this->executeFetch();
    }

    /**
     * Execute Query and fetch a single record as entity. Will always be the first.
     *
     * @return Entity|false Entity as successful result or false on not found.
     * @throws \Exception|\Throwable
     */
    public function one()
    {
        // Build query
        $this->buildQuery();

        return $this->executeFetch(false);
    }


    /**
     * Execute Query and fetch Entity/Entities.
     * This will postprocess the entities, and inject the state.
     *
     * @param bool $all Fetch all records as array
     *
     * @return Entity[]|Entity
     */
    private function executeFetch($all = true)
    {
        /** @var Entity|Entity{} $object */
        $object = Database::getInstance()->select($this->query, $this->whereBindValues, $this->whereBindTypes, $this->entityClass, $all);

        // If no result return false
        if ($object === false || count($object) === 0) {
            return false;
        }

        // Set the state for entities
        if ($all) {
            foreach ($object as $nr => $entity) { /** @var Entity $entity */
                $entity->_state = 1;
            }
        }else{
            $object->_state = 1;
        }

        return $object;
    }

    /**
     * Build Query.
     *
     * @throws \Exception|\Throwable
     */
    private function buildQuery()
    {
        // Prepare with checking for exceptions, once there is, throw it!
        if ($this->lastException !== null) {
            throw $this->lastException;
        }

        // Prepare the query
        $this->query = "SELECT * FROM " . Structure::getTable($this->entityClass)->getFullTableName();


        $this->buildWhere();
        // Add where to query
        if ($this->whereClause !== "") {
            $this->query .= " WHERE " . $this->whereClause;
        }


        // Order by
        if ($this->orderBy !== null) {
            $this->query .= " ORDER BY $this->orderBy $this->orderType";
        }

        // Limit
        if ($this->limit !== null) {
            $this->query .= " LIMIT $this->limit";

            if ($this->offset !== null) {
                $this->query .= ",$this->offset";
            }
        }
    }


    /**
     * Build Where clause, textual and binding parameters
     */
    private function buildWhere()
    {
        // Reset
        $this->whereClause = "";
        $this->whereBindValues = array();
        $this->whereBindTypes = array();

        // Check for empty where
        if (count($this->where) == 0) {
            return;
        }

        // Now we will loop through our where criteria's
        $idx = 0;
        $max = count($this->where);
        foreach ($this->where as $id => $criteria) {
            $column = $criteria['column'];
            $operator = $criteria['operator'];
            $value = $criteria['value'];

            // Prepare where clause
            $this->whereClause .= "$column ";

            // Check our operator
            if ($operator === "IN") {
                // Prepare and loop
                $this->whereClause .= "IN (";

                $subIdx = 0;
                $subMax = count($value);
                foreach ($value as $subValue) {
                    $this->addWhere($subValue);
                    if (($subIdx+1) < $subMax) {
                        $this->whereClause .= ",";
                    }
                    $subIdx++;
                }
                $this->whereClause .= ")";
            } else {
                // Will add a normal operator
                $this->whereClause .= "$operator ";
                $this->addWhere($value);
            }

            // Adding AND if not last
            if (($idx + 1) < $max) {
                $this->whereClause .= " AND ";
            }
            $idx++;
        }
    }


    /**
     * Add entry to our where clause. Will add a '?' to the clause and the value + type into our array
     *
     * @param mixed $value
     */
    private function addWhere($value)
    {
        $this->whereClause .= "?";
        $this->whereBindValues[] = $value;
        if (is_bool($value)) {
            $this->whereBindTypes[] = PDO::PARAM_BOOL;
        }elseif(is_int($value)) {
            $this->whereBindTypes[] = PDO::PARAM_INT;
        }elseif($value === null) {
            $this->whereBindTypes[] = PDO::PARAM_NULL;
        }else{
            $this->whereBindTypes[] = PDO::PARAM_STR;
        }
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
