<?php
/**
 * Query - A simple Database QueryBuilder.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Database\Query;

use Helpers\Inflector;
use Database\Connection;
use Database\Query\Expression;
use Database\Query\Grammar;
use Database\Query\JoinClause;
use Database\Query\Processor;
use Support\Facades\Paginator;

use PDO;
use Closure;


class Builder
{
    /**
     * The Database Connection instance.
     *
     * @var \Database\Connection
     */
    protected $connection;

    /**
     * The Database Query Grammar instance.
     *
     * @var \Database\Query\Grammar
     */
    protected $grammar;

    /**
     * The Database Query Processor instance.
     *
     * @var \Database\Query\Processor
     */
    protected $processor;

    /**
     * The current query value bindings.
     *
     * @var array
     */
    protected $bindings = array();

    /**
     * An aggregate function and column to be run.
     *
     * @var array
     */
    public $aggregate;

    /**
     * The columns that should be returned.
     *
     * @var array
     */
    public $columns;

    /**
     * Indicates if the query returns distinct results.
     *
     * @var bool
     */
    public $distinct = false;

    /**
     * The table which the query is targeting.
     *
     * @var string
     */
    public $from;

    /**
     * The table joins for the query.
     *
     * @var array
     */
    public $joins;

    /**
     * The WHERE constraints for the query.
     *
     * @var array
     */
    public $wheres;

    /**
     * The GROUP BY clauses.
     *
     * @var array
     */
    public $groups;

    /**
     * The HAVING constraints for the query.
     *
     * @var array
     */
    public $havings;

    /**
     * The orderings for the query.
     *
     * @var array
     */
    public $orders;

    /**
     * The maximum number of records to return.
     *
     * @var int
     */
    public $limit;

    /**
     * The number of records to skip.
     *
     * @var int
     */
    public $offset;

    /**
     * The query UNION statements.
     *
     * @var array
     */
    public $unions;

    /**
     * The maximum number of union records to return.
     *
     * @var int
     */
    public $unionLimit;

    /**
     * The number of union records to skip.
     *
     * @var int
     */
    public $unionOffset;

    /**
     * The orderings for the union query.
     *
     * @var array
     */
    public $unionOrders;

    /**
     * Indicates whether row locking is being used.
     *
     * @var string|bool
     */
    public $lock;

    /**
     * All of the available clause operators.
     *
     * @var array
     */
    protected $operators = array(
        '=', '<', '>', '<=', '>=', '<>', '!=',
        'like', 'not like', 'between', 'ilike',
        '&', '|', '^', '<<', '>>',
    );

    /**
     * Create a new Query instance.
     *
     * @param  \Database\Connection  $connection
     * @param  \Database\Query\Grammar  $grammar
     * @param  \Database\Query\Processor  $processor
     * @return void
     */
    public function __construct(Connection $connection, Grammar $grammar, Processor $processor)
    {
        $this->connection = $connection;

        $this->grammar = $grammar;

        $this->processor = $processor;
    }

    /**
     * Set the columns to be selected.
     *
     * @param  array  $columns
     * @return \Database\Query\Builder|static
     */
    public function select($columns = array('*'))
    {
        $this->columns = is_array($columns) ? $columns : func_get_args();

        return $this;
    }

    /**
     * Add a new "raw" select expression to the query.
     *
     * @param  string  $expression
     * @return \Database\Query\Builder|static
     */
    public function selectRaw($expression)
    {
        return $this->select(new Expression($expression));
    }

    /**
     * Add a new select column to the query.
     *
     * @param  mixed  $column
     * @return \Database\Query\Builder|static
     */
    public function addSelect($column)
    {
        $column = is_array($column) ? $column : func_get_args();

        $this->columns = array_merge((array) $this->columns, $column);

        return $this;
    }

    /**
     * Force the query to only return distinct results.
     *
     * @return \Database\Query\Builder|static
     */
    public function distinct()
    {
        $this->distinct = true;

        return $this;
    }

    /**
     * Set the table which the query is targeting.
     *
     * @param  string  $table
     * @return \Database\Query\Builder|static
     */
    public function from($table)
    {
        $this->from = $table;

        return $this;
    }

    /**
     * Add a "JOIN" clause to the query.
     *
     * @param  string  $table
     * @param  string  $first
     * @param  string  $operator
     * @param  string  $two
     * @param  string  $type
     * @param  bool  $where
     * @return \Database\Query\Builder|static
     */
    public function join($table, $one, $operator = null, $two = null, $type = 'inner', $where = false)
    {
        if ($one instanceof Closure) {
            $this->joins[] = new JoinClause($this, $type, $table);

            call_user_func($one, end($this->joins));
        } else {
            $join = new JoinClause($this, $type, $table);

            $this->joins[] = $join->on($one, $operator, $two, 'and', $where);
        }

        return $this;
    }

    /**
     * Add a "JOIN WHERE" clause to the query.
     *
     * @param  string  $table
     * @param  string  $first
     * @param  string  $operator
     * @param  string  $two
     * @param  string  $type
     * @return \Database\Query\Builder|static
     */
    public function joinWhere($table, $one, $operator, $two, $type = 'inner')
    {
        return $this->join($table, $one, $operator, $two, $type, true);
    }

    /**
     * Add a "LEFT JOIN" to the query.
     *
     * @param  string  $table
     * @param  string  $first
     * @param  string  $operator
     * @param  string  $second
     * @return \Database\Query\Builder|static
     */
    public function leftJoin($table, $first, $operator = null, $second = null)
    {
        return $this->join($table, $first, $operator, $second, 'left');
    }

    /**
     * Add a "LEFT JOIN WHERE" clause to the query.
     *
     * @param  string  $table
     * @param  string  $first
     * @param  string  $operator
     * @param  string  $two
     * @return \Database\Query\Builder|static
     */
    public function leftJoinWhere($table, $one, $operator, $two)
    {
        return $this->joinWhere($table, $one, $operator, $two, 'left');
    }

    /**
     * Add a raw "WHERE" condition to the query.
     *
     * @param  string  $where
     * @param  array   $bindings
     * @param  string  $boolean
     * @return \Database\Query\Builder|static
     */
    public function whereRaw($where, $bindings = array(), $boolean = 'and')
    {
        $type = 'Raw';

        $this->wheres[] = compact('type', 'sql', 'boolean');

        $this->bindings = array_merge($this->bindings, $bindings);

        return $this;
    }

    /**
     * Add a raw "OR WHERE" condition to the query.
     *
     * @param  string  $where
     * @param  array   $bindings
     * @return \Database\Query\Builder|static
     */
    public function orWhereRaw($where, $bindings = array())
    {
        return $this->whereRaw($where, $bindings, 'or');
    }

    /**
     * Add a basic "WHERE" clause to the query.
     *
     * @param  string  $column
     * @param  string  $operator
     * @param  mixed   $value
     * @param  string  $boolean
     * @return \Database\Query\Builder|static
     *
     * @throws \InvalidArgumentException
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if (func_num_args() == 2) {
            list($value, $operator) = array($operator, '=');
        } else if ($this->invalidOperatorAndValue($operator, $value)) {
            throw new \InvalidArgumentException("A value must be provided.");
        }

        if ($column instanceof Closure) {
            return $this->whereNested($column, $boolean);
        }

        if (! in_array(strtolower($operator), $this->operators, true)) {
            list($value, $operator) = array($operator, '=');
        }

        if ($value instanceof Closure) {
            return $this->whereSub($column, $operator, $value, $boolean);
        }

        if (is_null($value)) {
            return $this->whereNull($column, $boolean, ($operator != '='));
        }

        $type = 'Basic';

        $this->wheres[] = compact('type', 'column', 'operator', 'value', 'boolean');

        $this->bindings[] = $value;

        return $this;
    }

    /**
     * Add an "OR WHERE" clause to the query.
     *
     * @param  string  $column
     * @param  string  $operator
     * @param  mixed   $value
     * @return \Database\Query\Builder|static
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        return $this->where($column, $operator, $value, 'or');
    }

    /**
     * Determine if the given operator and value combination is legal.
     *
     * @param  string  $operator
     * @param  mixed  $value
     * @return bool
     */
    protected function invalidOperatorAndValue($operator, $value)
    {
        $isOperator = in_array($operator, $this->operators);

        return ($isOperator && ($operator != '=') && is_null($value));
    }

    /**
     * Add a "WHERE BETWEEN" statement to the query.
     *
     * @param  string  $column
     * @param  array   $values
     * @param  string  $boolean
     * @param  bool  $not
     * @return \Database\Query\Builder|static
     */
    public function whereBetween($column, array $values, $boolean = 'and', $not = false)
    {
        $type = 'Between';

        $this->wheres[] = compact('column', 'type', 'boolean', 'not');

        $this->bindings = array_merge($this->bindings, $values);

        return $this;
    }

    /**
     * Add an "OR WHERE BETWEEN" statement to the query.
     *
     * @param  string  $column
     * @param  array   $values
     * @return \Database\Query\Builder|static
     */
    public function orWhereBetween($column, array $values)
    {
        return $this->whereBetween($column, $values, 'or');
    }

    /**
     * Add a "WHERE NOT BETWEEN" statement to the query.
     *
     * @param  string  $column
     * @param  array   $values
     * @param  string  $boolean
     * @return \Database\Query\Builder|static
     */
    public function whereNotBetween($column, array $values, $boolean = 'and')
    {
        return $this->whereBetween($column, $values, $boolean, true);
    }

    /**
     * Add an "OR WHERE NOT BETWEEN" statement to the query.
     *
     * @param  string  $column
     * @param  array   $values
     * @return \Database\Query\Builder|static
     */
    public function orWhereNotBetween($column, array $values)
    {
        return $this->whereNotBetween($column, $values, 'or');
    }

    /**
     * Add a nested where statement to the query.
     *
     * @param  \Closure $callback
     * @param  string   $boolean
     * @return \Database\Query\Builder|static
     */
    public function whereNested(Closure $callback, $boolean = 'and')
    {
        $query = $this->newQuery();

        $query->from($this->from);

        call_user_func($callback, $query);

        return $this->addNestedWhereQuery($query, $boolean);
    }

    /**
     * Add another query builder as a nested where to the query builder.
     *
     * @param  \Database\Query\Builder $query
     * @param  string  $boolean
     * @return \Database\Query\Builder|static
     */
    public function addNestedWhereQuery($query, $boolean = 'and')
    {
        if (count($query->wheres)) {
            $type = 'Nested';

            $this->wheres[] = compact('type', 'query', 'boolean');

            $this->mergeBindings($query);
        }

        return $this;
    }

    /**
     * Add a full sub-select to the query.
     *
     * @param  string   $column
     * @param  string   $operator
     * @param  \Closure $callback
     * @param  string   $boolean
     * @return \Database\Query\Builder|static
     */
    protected function whereSub($column, $operator, Closure $callback, $boolean)
    {
        $type = 'Sub';

        $query = $this->newQuery();

        call_user_func($callback, $query);

        $this->wheres[] = compact('type', 'column', 'operator', 'query', 'boolean');

        $this->mergeBindings($query);

        return $this;
    }

    /**
     * Add an exists clause to the query.
     *
     * @param  \Closure $callback
     * @param  string   $boolean
     * @param  bool     $not
     * @return \Database\Query\Builder|static
     */
    public function whereExists(Closure $callback, $boolean = 'and', $not = false)
    {
        $type = $not ? 'NotExists' : 'Exists';

        $query = $this->newQuery();

        // Similar to the sub-select clause, we will create a new query instance so
        // the developer may cleanly specify the entire exists query and we will
        // compile the whole thing in the grammar and insert it into the SQL.
        call_user_func($callback, $query);

        $this->wheres[] = compact('type', 'operator', 'query', 'boolean');

        $this->mergeBindings($query);

        return $this;
    }

    /**
     * Add an or exists clause to the query.
     *
     * @param  \Closure $callback
     * @param  bool     $not
     * @return \Database\Query\Builder|static
     */
    public function orWhereExists(Closure $callback, $not = false)
    {
        return $this->whereExists($callback, 'or', $not);
    }

    /**
     * Add a where not exists clause to the query.
     *
     * @param  \Closure $callback
     * @param  string   $boolean
     * @return \Database\Query\Builder|static
     */
    public function whereNotExists(Closure $callback, $boolean = 'and')
    {
        return $this->whereExists($callback, $boolean, true);
    }

    /**
     * Add a where not exists clause to the query.
     *
     * @param  \Closure  $callback
     * @return \Database\Query\Builder|static
     */
    public function orWhereNotExists(Closure $callback)
    {
        return $this->orWhereExists($callback, true);
    }

    /**
     * Add a "WHERE IN" clause to the query.
     *
     * @param  string  $column
     * @param  mixed   $values
     * @param  string  $boolean
     * @param  bool    $not
     * @return \Database\Query\Builder|static
     */
    public function whereIn($column, $values, $boolean = 'and', $not = false)
    {
        $type = $not ? 'NotIn' : 'In';

        if ($values instanceof Closure) {
            return $this->whereInSub($column, $values, $boolean, $not);
        }

        $this->wheres[] = compact('type', 'column', 'values', 'boolean');

        $this->bindings = array_merge($this->bindings, $values);

        return $this;
    }

    /**
     * Add an "OR WHERE IN" clause to the query.
     *
     * @param  string  $column
     * @param  mixed   $values
     * @return \Database\Query\Builder|static
     */
    public function orWhereIn($column, $values)
    {
        return $this->whereIn($column, $values, 'or');
    }

    /**
     * Add a "WHERE NOT IN" clause to the query.
     *
     * @param  string  $column
     * @param  mixed   $values
     * @param  string  $boolean
     * @return \Database\Query\Builder|static
     */
    public function whereNotIn($column, $values, $boolean = 'and')
    {
        return $this->whereIn($column, $values, $boolean, true);
    }

    /**
     * Add an "OR WHERE NOT IN" clause to the query.
     *
     * @param  string  $column
     * @param  mixed   $values
     * @return \Database\Query\Builder|static
     */
    public function orWhereNotIn($column, $values)
    {
        return $this->whereNotIn($column, $values, 'or');
    }

    /**
     * Add a where in with a sub-select to the query.
     *
     * @param  string   $column
     * @param  \Closure $callback
     * @param  string   $boolean
     * @param  bool     $not
     * @return \Database\Query\Builder|static
     */
    protected function whereInSub($column, Closure $callback, $boolean, $not)
    {
        $type = $not ? 'NotInSub' : 'InSub';

        call_user_func($callback, $query = $this->newQuery());

        $this->wheres[] = compact('type', 'column', 'query', 'boolean');

        $this->mergeBindings($query);

        return $this;
    }

    /**
     * Add a "WHERE NULL" clause to the query.
     *
     * @param  string  $column
     * @param  string  $boolean
     * @param  bool    $not
     * @return \Database\Query\Builder|static
     */
    public function whereNull($column, $boolean = 'and', $not = false)
    {
        $type = $not ? 'NotNull' : 'Null';

        $this->wheres[] = compact('type', 'column', 'boolean');

        return $this;
    }

    /**
     * Add an "OR WHERE NULL" clause to the query.
     *
     * @param  string  $column
     * @return \Database\Query\Builder|static
     */
    public function orWhereNull($column)
    {
        return $this->whereNull($column, 'or');
    }

    /**
     * Add a "WHERE NOT NULL" clause to the query.
     *
     * @param  string  $column
     * @param  string  $boolean
     * @return \Database\Query\Builder|static
     */
    public function whereNotNull($column, $boolean = 'and')
    {
        return $this->whereNull($column, $boolean, true);
    }

    /**
     * Add an "OR WHERE NOT NULL" clause to the query.
     *
     * @param  string  $column
     * @return \Database\Query\Builder|static
     */
    public function orWhereNotNull($column)
    {
        return $this->whereNotNull($column, 'or');
    }

    /**
     * Add a "WHERE DAY" statement to the query.
     *
     * @param  string  $column
     * @param  string   $operator
     * @param  int   $value
     * @param  string   $boolean
     * @return \Database\Query\Builder|static
     */
    public function whereDay($column, $operator, $value, $boolean = 'and')
    {
        return $this->addDateBasedWhere('Day', $column, $operator, $value, $boolean);
    }

    /**
     * Add a "WHERE MONTH" statement to the query.
     *
     * @param  string  $column
     * @param  string   $operator
     * @param  int   $value
     * @param  string   $boolean
     * @return \Database\Query\Builder|static
     */
    public function whereMonth($column, $operator, $value, $boolean = 'and')
    {
        return $this->addDateBasedWhere('Month', $column, $operator, $value, $boolean);
    }

    /**
     * Add a "WHERE YEAR" statement to the query.
     *
     * @param  string  $column
     * @param  string   $operator
     * @param  int   $value
     * @param  string   $boolean
     * @return \Database\Query\Builder|static
     */
    public function whereYear($column, $operator, $value, $boolean = 'and')
    {
        return $this->addDateBasedWhere('Year', $column, $operator, $value, $boolean);
    }

    /**
     * Add a date based (year, month, day) statement to the query.
     *
     * @param  string  $type
     * @param  string  $column
     * @param  string  $operator
     * @param  int  $value
     * @param  string  $boolean
     * @return \Database\Query\Builder|static
     */
    protected function addDateBasedWhere($type, $column, $operator, $value, $boolean = 'and')
    {
        $this->wheres[] = compact('column', 'type', 'boolean', 'operator', 'value');

        $this->bindings[] = $value;

        return $this;
    }

    /**
     * Handles dynamic "WHERE" clauses to the query.
     *
     * @param  string  $method
     * @param  string  $parameters
     * @return \Database\Query\Builder|static
     */
    public function dynamicWhere($method, $parameters)
    {
        $finder = substr($method, 5);

        $segments = preg_split('/(And|Or)(?=[A-Z])/', $finder, -1, PREG_SPLIT_DELIM_CAPTURE);

        $connector = 'and';

        $index = 0;

        foreach ($segments as $segment) {
            if (($segment != 'And') && ($segment != 'Or')) {
                $this->addDynamic($segment, $connector, $parameters, $index);

                $index++;
            } else {
                $connector = $segment;
            }
        }

        return $this;
   }

   /**
    * Add a single dynamic "WHERE" clause statement to the query.
    *
    * @param  string  $segment
    * @param  string  $connector
    * @param  array   $parameters
    * @param  int     $index
    * @return void
    */
    protected function addDynamic($segment, $connector, $parameters, $index)
    {
        $boolean = strtolower($connector);

        $this->where(Inflector::tableize($segment), '=', $parameters[$index], $boolean);
    }


    /**
     * Add a "HAVING" to the query.
     *
     * @param  string  $column
     * @param  string  $operator
     * @param  mixed   $value
     * @return \Database\Query\Builder|static
     */
    public function having($column, $operator = null, $value = null)
    {
        $type = 'Basic';

        $this->havings[] = compact('type', 'column', 'operator', 'value');

        $this->bindings[] = $value;

        return $this;
    }

    /**
     * Add a raw "HAVING" clause to the query.
     *
     * @param  string  $sql
     * @param  array   $bindings
     * @param  string  $boolean
     * @return \Database\Query\Builder|static
     */
    public function havingRaw($sql, array $bindings = array(), $boolean = 'and')
    {
        $type = 'Raw';

        $this->havings[] = compact('type', 'sql', 'boolean');

        $this->bindings = array_merge($this->bindings, $bindings);

        return $this;
    }

    /**
     * Add a raw "OR HAVING" clause to the query.
     *
     * @param  string  $sql
     * @param  array   $bindings
     * @return \Database\Query\Builder|static
     */
    public function orHavingRaw($sql, array $bindings = array())
    {
        return $this->havingRaw($sql, $bindings, 'or');
    }

    /**
     * Add a grouping to the query.
     *
     * @param  string  $column
     * @return \Database\Query\Builder|static
     */
    public function groupBy($column)
    {
        $this->groups = array_merge((array) $this->groups, func_get_args());

        return $this;
    }

    /**
     * Add an "ORDER BY" clause to the query.
     *
     * @param  string  $column
     * @param  string  $direction
     * @return \Database\Query\Builder|static
     */
    public function orderBy($column, $direction = 'asc')
    {
        $property = $this->unions ? 'unionOrders' : 'orders';

        $direction = strtolower($direction) == 'asc' ? 'ASC' : 'DESC';

        $this->{$property}[] = compact('column', 'direction');

        return $this;
    }

    /**
     * Add a raw "ORDER BY" clause to the query.
     *
     * @param  string  $sql
     * @param  array  $bindings
     * @return \Database\Query\Builder|static
     */
    public function orderByRaw($sql, $bindings = array())
    {
        $type = 'Raw';

        $this->orders[] = compact('type', 'sql');

        $this->bindings = array_merge($this->bindings, $bindings);

        return $this;
    }

    /**
     * Add an "ORDER BY" clause for a timestamp to the query.
     *
     * @param  string  $column
     * @return \Database\Query\Builder|static
     */
    public function latest($column = 'created_at')
    {
        return $this->orderBy($column, 'desc');
    }

    /**
     * Add an "ORDER BY" clause for a timestamp to the query.
     *
     * @param  string  $column
     * @return \Database\Query\Builder|static
     */
    public function oldest($column = 'created_at')
    {
        return $this->orderBy($column, 'asc');
    }

    /**
     * Set the "OFFSET" value of the query.
     *
     * @param  int  $value
     * @return \Database\Query\Builder|static
     */
    public function offset($value)
    {
        $property = $this->unions ? 'unionOffset' : 'offset';

        $this->$property = max(0, $value);

        return $this;
    }

    /**
     * Alias to set the "OFFSET" value of the query.
     *
     * @param  int  $value
     * @return \Database\Query\Builder|static
     */
    public function skip($value)
    {
        return $this->offset($value);
    }

    /**
     * Set the "LIMIT" value of the query.
     *
     * @param  int  $value
     * @return \Database\Query\Builder|static
     */
    public function limit($value)
    {
        $property = $this->unions ? 'unionLimit' : 'limit';

        if ($value > 0) $this->$property = $value;

        return $this;
    }

    /**
     * Alias to set the "LIMIT" value of the query.
     *
     * @param  int  $value
     * @return \Database\Query\Builder|static
     */
    public function take($value)
    {
        return $this->limit($value);
    }

    /**
     * Set the query limit and offset for a given page.
     *
     * @param  int    $page
     * @param  int    $perPage
     * @return \Database\Query\Builder|static
     */
    public function forPage($page, $perPage = 15)
    {
        return $this->skip(($page - 1) * $perPage)->take($perPage);
    }

    /**
     * Add a union statement to the query.
     *
     * @param  \Database\Query\Builder|\Closure  $query
     * @param  bool $all
     * @return \Database\Query\Builder|static
     */
    public function union($query, $all = false)
    {
        if ($query instanceof Closure) {
            call_user_func($query, $query = $this->newQuery());
        }

        $this->unions[] = compact('query', 'all');

        return $this->mergeBindings($query);
    }

    /**
     * Add a union all statement to the query.
     *
     * @param  \Database\Query\Builder|\Closure  $query
     * @return \Database\Query\Builder|static
     */
    public function unionAll($query)
    {
        return $this->union($query, true);
    }

    /**
     * Lock the selected rows in the table.
     *
     * @param  bool  $value
     * @return $this
     */
    public function lock($value = true)
    {
        $this->lock = $value;

        return $this;
    }

    /**
     * Lock the selected rows in the table for updating.
     *
     * @return \Database\Query\Builder
     */
    public function lockForUpdate()
    {
        return $this->lock(true);
    }

    /**
     * Share lock the selected rows in the table.
     *
     * @return \Database\Query\Builder
     */
    public function sharedLock()
    {
        return $this->lock(false);
    }

    /**
     * Execute a query for a single Record by ID.
     *
     * @param  int    $id
     * @param  array  $columns
     * @return mixed|static
     */
    public function find($id, $columns = array('*'))
    {
        return $this->where('id', '=', $id)->first($columns);
    }

    /**
     * Pluck a single column's value from the first result of a query.
     *
     * @param  string  $column
     * @return mixed
     */
    public function pluck($column)
    {
        $result = $this->first(array($column));

        if (! is_null($result)) {
            $result = (array) $result;
        }

        return (count($result) > 0) ? reset($result) : null;
    }

    /**
     * Execute the query and get the first result.
     *
     * @param  array   $columns
     * @return mixed
     */
    public function first($columns = array('*'))
    {
        $results = $this->take(1)->get($columns);

        return (count($results) > 0) ? reset($results) : null;
    }

    /**
     * Chunk the results of the query.
     *
     * @param  int  $count
     * @param  callable  $callback
     * @return void
     */
    public function chunk($count, $callback)
    {
        $results = $this->forPage($page = 1, $count)->get();

        while (count($results) > 0) {
            call_user_func($callback, $results);

            $page++;

            $results = $this->forPage($page, $count)->get();
        }
    }

    /**
     * Get an array with the values of a given Column.
     *
     * @param  string  $column
     * @param  string  $key
     * @return array
     */
    public function lists($column, $key = null)
    {
        $columns = is_null($key) ? array($column) : array($column, $key);

        $results = $this->get($columns);

        $values = array_map(function($row) use ($column) {
            return $row->{$column};
        }, $results);

        if (! is_null($key) && (count($results) > 0)) {
            return array_combine(array_map(function($row) use ($key) {
                return $row->{$key};
            }, $results), $values);
        }

        return $values;
    }

    /**
     * Concatenate values of a given Column as a string.
     *
     * @param  string  $column
     * @param  string  $glue
     * @return string
     */
    public function implode($column, $glue = null)
    {
        if (is_null($glue)) {
            return implode($this->lists($column));
        }

        return implode($glue, $this->lists($column));
    }

    /**
     * Get a paginator for the "SELECT" statement.
     *
     * @param  int    $perPage
     * @param  array  $columns
     * @return \Pagination\Paginator
     */
    public function paginate($perPage = 15, $columns = array('*'))
    {
        // Get the Pagination Factory instance.
        $paginator = $this->connection->getPaginator();

        if (isset($this->groups)) {
            return $this->groupedPaginate($paginator, $perPage, $columns);
        } else {
            return $this->ungroupedPaginate($paginator, $perPage, $columns);
        }
    }

    /**
     * Create a Paginator for a grouped pagination statement.
     *
     * @param  int    $perPage
     * @param  array  $columns
     * @return \Pagination\Paginator
     */
    protected function groupedPaginate($paginator, $perPage, $columns)
    {
        $results = $this->get($columns);

        return $this->buildRawPaginator($paginator, $results, $perPage);
    }

    /**
     * Build a paginator instance from a raw result array.
     *
     * @param  \Illuminate\Pagination\Factory  $paginator
     * @param  array  $results
     * @param  int    $perPage
     * @return \Pagination\Paginator
     */
    public function buildRawPaginator($paginator, $results, $perPage)
    {
        // For queries which have a group by, we will actually retrieve the entire set
        // of rows from the table and "slice" them via PHP. This is inefficient and
        // the developer must be aware of this behavior; however, it's an option.
        $start = ($paginator->getCurrentPage() - 1) * $perPage;

        $sliced = array_slice($results, $start, $perPage);

        return $paginator->make($sliced, count($results), $perPage);
    }

    /**
     * Create a paginator for an un-grouped pagination statement.
     *
     * @param  \Pagination\Environment  $paginator
     * @param  int    $perPage
     * @param  array  $columns
     * @return \Pagination\Paginator
     */
    protected function ungroupedPaginate($paginator, $perPage, $columns)
    {
        $total = $this->getPaginationCount();

        // Once we have the total number of records to be paginated, we can grab the
        // current page and the result array. Then we are ready to create a brand
        // new Paginator instances for the results which will create the links.
        $page = $paginator->getCurrentPage($total);

        $results = $this->forPage($page, $perPage)->get($columns);

        return $paginator->make($results, $total, $perPage);
    }

    /**
     * Get a paginator only supporting simple next and previous links.
     *
     * This is more efficient on larger data-sets, etc.
     *
     * @param  int    $perPage
     * @param  array  $columns
     * @return \Pagination\Paginator
     */
    public function simplePaginate($perPage = 15, $columns = array('*'))
    {
        // Get the Pagination Factory instance.
        $paginator = Paginator::instance();

        $page = $paginator->getCurrentPage();

        $this->skip(($page - 1) * $perPage)->take($perPage + 1);

        return $paginator->make($this->get($columns), $perPage);
    }

    /**
     * Get the count of the total records for pagination.
     *
     * @return int
     */
    public function getPaginationCount()
    {
        $this->backupFieldsForCount();

        $total = $this->count();

        $this->restoreFieldsForCount();

        return $total;
    }

    /**
     * Backup certain fields for a pagination count.
     *
     * @return void
     */
    protected function backupFieldsForCount()
    {
        foreach (array('orders', 'limit', 'offset') as $field) {
            $this->backups[$field] = $this->{$field};

            $this->{$field} = null;
        }
    }

    /**
     * Restore certain fields for a pagination count.
     *
     * @return void
     */
    protected function restoreFieldsForCount()
    {
        foreach (array('orders', 'limit', 'offset') as $field) {
            $this->{$field} = $this->backups[$field];
        }

        $this->backups = array();
    }

    /**
     * Execute the query as a "SELECT" statement.
     *
     * @param  array  $columns
     * @return array
     */
    public function get($columns = array('*'))
    {
        if (is_null($this->columns)) {
            $this->columns = $columns;
        }

        return $this->runSelect();
    }

    /**
     * Run the query as a "SELECT" statement against the Connection.
     *
     * @return array
     */
    protected function runSelect()
    {
        return $this->connection->select($this->toSql(), $this->getBindings());
    }

    /**
     * Get the SQL representation of the query.
     *
     * @return string
     */
    public function toSql()
    {
        return $this->grammar->compileSelect($this);
    }

    /**
     * Determine if any rows exist for the current query.
     *
     * @return bool
     */
    public function exists()
    {
        return ($this->count() > 0);
    }

    /**
     * Retrieve the "COUNT" result of the query.
     *
     * @param  string  $column
     * @return int
     */
    public function count($column = '*')
    {
        return (int) $this->aggregate(__FUNCTION__, array($column));
    }

    /**
     * Retrieve the minimum value of a given column.
     *
     * @param  string  $column
     * @return mixed
     */
    public function min($column)
    {
        return $this->aggregate(__FUNCTION__, array($column));
    }

    /**
     * Retrieve the maximum value of a given column.
     *
     * @param  string  $column
     * @return mixed
     */
    public function max($column)
    {
        return $this->aggregate(__FUNCTION__, array($column));
    }

    /**
     * Retrieve the sum of the values of a given column.
     *
     * @param  string  $column
     * @return mixed
     */
    public function sum($column)
    {
        return $this->aggregate(__FUNCTION__, array($column));
    }

    /**
     * Retrieve the average of the values of a given column.
     *
     * @param  string  $column
     * @return mixed
     */
    public function avg($column)
    {
        return $this->aggregate(__FUNCTION__, array($column));
    }

    /**
     * Execute an aggregate function on the database.
     *
     * @param  string  $function
     * @param  array   $columns
     * @return mixed
     */
    public function aggregate($function, $columns = array('*'))
    {
        $this->aggregate = compact('function', 'columns');

        $previousColumns = $this->columns;

        $results = $this->get($columns);

        // Once we have executed the query, we will reset the aggregate property.
        $this->aggregate = null;

        $this->columns = $previousColumns;

        if (isset($results[0])) {
            $result = array_change_key_case((array) $results[0]);

            return $result['aggregate'];
        }
    }

    /**
     * Insert a new record into the database.
     *
     * @param  array  $values
     * @return bool
     */
    public function insert(array $values)
    {
        if ( ! is_array(reset($values))) {
            $values = array($values);
        } else {
            foreach ($values as $key => $value) {
                ksort($value); $values[$key] = $value;
            }
        }

        $bindings = array();

        foreach ($values as $record) {
            foreach ($record as $value) {
                $bindings[] = $value;
            }
        }

        $sql = $this->grammar->compileInsert($this, $values);

        $bindings = $this->cleanBindings($bindings);

        return $this->connection->insert($sql, $bindings);
    }

    /**
     * Insert a new Record and get the value of the primary key.
     *
     * @param  array   $values
     * @return int
     */
    public function insertGetId(array $values)
    {
        $sql = $this->grammar->compileInsertGetId($this, $values, $sequence);

        $values = $this->cleanBindings($values);

        return $this->processor->processInsertGetId($this, $sql, $values, $sequence);
    }

    /**
     * Update a Record in the database.
     *
     * @param  array  $values
     * @return int
     */
    public function update(array $values)
    {
        $bindings = array_values(array_merge($values, $this->getBindings()));

        $sql = $this->grammar->compileUpdate($this, $values);

        return $this->connection->update($sql, $this->cleanBindings($bindings));
    }

    /**
     * Increment a Column's value by a given amount.
     *
     * @param  string  $column
     * @param  int     $amount
     * @param  array   $extra
     * @return int
     */
    public function increment($column, $amount = 1, array $extra = array())
    {
        $wrapped = $this->grammar->wrap($column);

        $columns = array_merge(array($column => $this->raw("$wrapped + $amount")), $extra);

        return $this->update($columns);
    }

    /**
     * Decrement a Column's value by a given amount.
     *
     * @param  string  $column
     * @param  int     $amount
     * @param  array   $extra
     * @return int
     */
    public function decrement($column, $amount = 1, array $extra = array())
    {
        $wrapped = $this->grammar->wrap($column);

        $columns = array_merge(array($column => $this->raw("$wrapped - $amount")), $extra);

        return $this->update($columns);
    }

    /**
     * Delete a Record from the database.
     *
     * @return int
     */
    public function delete($id = null)
    {
        if (! is_null($id)) $this->where('id', '=', $id);

        $sql = $this->grammar->compileDelete($this);

        return $this->connection->delete($sql, $this->getBindings());
    }

    /**
     * Run a TRUNCATE statement on the table.
     *
     * @return void
     */
    public function truncate()
    {
        foreach ($this->grammar->compileTruncate($this) as $sql => $bindings) {
            $this->connection->statement($sql, $bindings);
        }
    }

    /**
     * Get a new instance of the QueryBuilder.
     *
     * @return \Database\Query\Builder|static
     */
    public function newQuery()
    {
        return new Builder($this->connection, $this->grammar, $this->processor);
    }

    /**
     * Merge an array of where clauses and bindings.
     *
     * @param  array  $wheres
     * @param  array  $bindings
     * @return void
     */
    public function mergeWheres($wheres, $bindings)
    {
        $this->wheres = array_merge((array) $this->wheres, (array) $wheres);

        $this->bindings['where'] = array_values(array_merge($this->bindings['where'], (array) $bindings));
    }

    /**
     * Create a raw Database Expression.
     *
     * @param  mixed  $value
     * @return \Database\Query\Expression
     */
    public function raw($value)
    {
        return $this->connection->raw($value);
    }

    /**
     * Get the current query value bindings.
     *
     * @return array
     */
    public function getBindings()
    {
        return array_flatten($this->bindings);
    }

    /**
     * Get the raw array of bindings.
     *
     * @return array
     */
    public function getRawBindings()
    {
        return $this->bindings;
    }

    /**
     * Set the bindings on the query builder.
     *
     * @param  array   $bindings
     * @param  string  $type
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setBindings(array $bindings, $type = 'where')
    {
        if (! array_key_exists($type, $this->bindings)) {
            throw new \InvalidArgumentException("Invalid binding type: {$type}.");
        }

        $this->bindings[$type] = $bindings;

        return $this;
    }

    /**
     * Add a binding to the query.
     *
     * @param  mixed   $value
     * @param  string  $type
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function addBinding($value, $type = 'where')
    {
        if ( ! array_key_exists($type, $this->bindings)) {
            throw new \InvalidArgumentException("Invalid binding type: {$type}.");
        }

        if (is_array($value)) {
            $this->bindings[$type] = array_values(array_merge($this->bindings[$type], $value));
        } else {
            $this->bindings[$type][] = $value;
        }

        return $this;
    }

    /**
     * Merge an array of bindings into our bindings.
     *
     * @param  \Database\Query\Builder  $query
     * @return \Database\Query\Builder|static
     */
    public function mergeBindings(Builder $query)
    {
        $this->bindings = array_values(array_merge($this->bindings, $query->bindings));

        return $this;
    }

    /**
     * Remove all of the expressions from a list of bindings.
     *
     * @param  array  $bindings
     * @return array
     */
    protected function cleanBindings(array $bindings)
    {
        return array_values(array_filter($bindings, function($binding)
        {
            return (! $binding instanceof Expression);
        }));
    }

    /**
     * Get the Database Connection instance.
     *
     * @return \Database\Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Get the query grammar instance.
     *
     * @return \Database\Grammar
     */
    public function getGrammar()
    {
        return $this->grammar;
    }

    //--------------------------------------------------------------------
    // Magic Methods
    //--------------------------------------------------------------------

    /**
     * Handle dynamic method calls into the method.
     *
     * @param  string  $method
     * @param  array   $params
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $params)
    {
        if (str_starts_with($method, 'where')) {
            return $this->dynamicWhere($method, $params);
        }

        $className = get_class($this);

        throw new \BadMethodCallException("Call to undefined method {$className}::{$method}()");
    }

}
