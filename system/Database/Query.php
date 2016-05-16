<?php
/**
 * Query - A simple Database QueryBuilder.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Database;

use Helpers\Inflector;
use Database\Connection;
use Database\Expression;
use Database\JoinClause;

use \PDO;
use \Closure;


class Query
{
    /**
     * The database connection instance.
     *
     * @var \Database\Connection
     */
    protected $db;

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
     * The keyword identifier wrapper format.
     *
     * @var string
     */
    protected $wrapper = '`%s`';

    /**
     * All of the available clause operators.
     *
     * @var array
     */
    protected $operators = array(
        '=', '<', '>', '<=', '>=', '<>', '!=', 'like', 'not like', 'between', 'ilike', '&', '|', '^', '<<', '>>',
    );

    /**
     * Create a new Query instance.
     *
     * @return void
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Handle dynamic method calls into the method.
     *
     * @param  string  $method
     * @param  array   $params
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if (str_starts_with($method, 'where')) {
            return $this->dynamicWhere($method, $parameters);
        }

        $className = get_class($this);

        throw new \BadMethodCallException("Call to undefined method {$className}::{$method}()");
    }

    /**
     * Create a new Query instance.
     *
     * @return \Database\Query
     */
    public function newQuery()
    {
        $query = new Query($this->db);

        return $query;
    }

    /**
     * Set the columns to be selected.
     *
     * @param  array  $columns
     * @return \Database\Query
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
     * @return \Database\Query|static
     */
    public function selectRaw($expression)
    {
        return $this->select(new Expression($expression));
    }

    /**
     * Force the query to only return distinct results.
     *
     * @return \Database\Query
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
     * @return \Database\Query
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
     * @return \Database\Query
     */
    public function join($table, $one, $operator = null, $two = null, $type = 'inner', $where = false)
    {
        if ($one instanceof Closure) {
            $this->joins[] = new JoinClause($this, $type, $table);

            call_user_func($one, end($this->joins));
        } else {
            $join = new JoinClause($this, $type, $table);

            $this->joins[] = $join->on(
                $one, $operator, $two, 'and', $where
            );
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
     * @return \Database\Query
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
     * @return \Database\Query
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
     * @return \Database\Query
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
     * @return Query
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
     * @return Query
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
     * @return \Database\Query
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
     * @return \Database\Query
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
     * @return \Database\Query
     */
    public function whereBetween($column, array $values, $boolean = 'and', $not = false)
    {
        $type = 'between';

        $this->wheres[] = compact('column', 'type', 'boolean', 'not');

        $this->bindings = array_merge($this->bindings, $values);

        return $this;
    }

    /**
     * Add an "OR WHERE BETWEEN" statement to the query.
     *
     * @param  string  $column
     * @param  array   $values
     * @return \Database\Query
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
     * @return \Database\Query
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
     * @return \Database\Query
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
     * @return \Database\Query
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
     * @param  \Database\Query $query
     * @param  string  $boolean
     * @return \Database\Query
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
     * @return \Database\Query
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
     * Add a "WHERE IN" clause to the query.
     *
     * @param  string  $column
     * @param  mixed   $values
     * @param  string  $boolean
     * @param  bool    $not
     * @return \Database\Query
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
     * @return \Database\Query
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
     * @return \Database\Query
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
     * @return \Database\Query
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
     * @return \Database\Query
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
     * @return \Database\Query
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
     * @return \Database\Query
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
     * @return \Database\Query
     */
    public function whereNotNull($column, $boolean = 'and')
    {
        return $this->whereNull($column, $boolean, true);
    }

    /**
     * Add an "OR WHERE NOT NULL" clause to the query.
     *
     * @param  string  $column
     * @return \Database\Query
     */
    public function orWhereNotNull($column)
    {
        return $this->whereNotNull($column, 'or');
    }

    /**
     * Handles dynamic "WHERE" clauses to the query.
     *
     * @param  string  $method
     * @param  string  $parameters
     * @return \Database\Query|static
     */
    public function dynamicWhere($method, $parameters)
    {
        $finder = substr($method, 5);

        $segments = preg_split('/(And|Or)(?=[A-Z])/', $finder, -1, PREG_SPLIT_DELIM_CAPTURE);

        $connector = 'and';

        $index = 0;

        foreach ($segments as $segment) {
            if ($segment != 'And' && $segment != 'Or') {
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
        $bool = strtolower($connector);

        $this->where(Inflector::tableize($segment), '=', $parameters[$index], $bool);
    }


    /**
     * Add a "HAVING" to the query.
     *
     * @param  string  $column
     * @param  string  $operator
     * @param  mixed   $value
     */
    public function having($column, $operator, $value)
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
     * @return \Database\Query|static
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
     * @return \Database\Query|static
     */
    public function orHavingRaw($sql, array $bindings = array())
    {
        return $this->havingRaw($sql, $bindings, 'or');
    }

    /**
     * Add a grouping to the query.
     *
     * @param  string  $column
     * @return Query
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
     * @return \Database\Query
     */
    public function orderBy($column, $direction = 'asc')
    {
        $direction = (strtolower($direction) == 'asc') ? 'ASC' : 'DESC';

        $this->orders[] = compact('column', 'direction');

        return $this;
    }

    /**
     * Add a raw "order by" clause to the query.
     *
     * @param  string  $sql
     * @param  array  $bindings
     * @return \Database\Query|static
     */
    public function orderByRaw($sql, $bindings = array())
    {
        $type = 'Raw';

        $this->orders[] = compact('type', 'sql');

        $this->bindings = array_merge($this->bindings, $bindings);

        return $this;
    }

    /**
     * Set the "OFFSET" value of the query.
     *
     * @param  int  $value
     * @return \Database\Query
     */
    public function offset($value)
    {
        $this->offset = max(0, $value);

        return $this;
    }

    /**
     * Alias to set the "OFFSET" value of the query.
     *
     * @param  int  $value
     * @return \Database\Query
     */
    public function skip($value)
    {
        return $this->offset($value);
    }

    /**
     * Set the "LIMIT" value of the query.
     *
     * @param  int  $value
     * @return \Database\Query
     */
    public function limit($value)
    {
        if ($value > 0) {
            $this->limit = $value;
        }

        return $this;
    }

    /**
     * Alias to set the "LIMIT" value of the query.
     *
     * @param  int  $value
     * @return \Database\Query
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
     * @return Query
     */
    public function forPage($page, $perPage = 15)
    {
        return $this->skip(($page - 1) * $perPage)->take($perPage);
    }

    /**
     * Add a union statement to the query.
     *
     * @param  \Database\Query|\Closure  $query
     * @param  bool $all
     * @return \Database\Query|static
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
     * @param  \Database\Query|\Closure  $query
     * @return \Database\Query|static
     */
    public function unionAll($query)
    {
        return $this->union($query, true);
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

        $results = $this->runSelect();

        return $results;
    }

    /**
     * Run the query as a "SELECT" statement against the Connection.
     *
     * @return array
     */
    protected function runSelect()
    {
        return $this->db->select($this->toSql(), $this->bindings);
    }

    /**
     * Get the SQL representation of the query.
     *
     * @return string
     */
    public function toSql()
    {
        return $this->compileSelect($this);
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

        $results = $this->get($columns);

        $this->columns = null;

        $this->aggregate = null;

        if (count($results) > 0) {
            $result = (array) reset($results);

            return $result['aggregate'];
        }
    }

    /**
     * Increment the value of a column by a given amount.
     *
     * @param  string  $column
     * @param  int     $amount
     * @return int
     */
    public function increment($column, $amount = 1)
    {
        return $this->adjust($column, $amount, ' + ');
    }

    /**
     * Decrement the value of a column by a given amount.
     *
     * @param  string  $column
     * @param  int     $amount
     * @return int
     */
    public function decrement($column, $amount = 1)
    {
        return $this->adjust($column, $amount, ' - ');
    }

    /**
     * Adjust the value of a column up or down by a given amount.
     *
     * @param  string  $column
     * @param  int     $amount
     * @param  string  $operator
     * @return int
     */
    protected function adjust($column, $amount, $operator)
    {
        $wrapped = $this->wrap($column);

        $value = new Expresssion($wrapped .$operator .$amount);

        return $this->update(array($column => $value));
    }

    /**
     * Insert a new record into the database.
     *
     * @param  array  $values
     * @return bool
     */
    public function insert(array $values)
    {
        if (! is_array(reset($values))) {
            $values = array($values);
        } else {
            foreach ($values as $key => $value) {
                ksort($value);

                $values[$key] = $value;
            }
        }

        $bindings = array();

        foreach ($values as $record) {
            $bindings = array_merge($bindings, array_values($record));
        }

        $sql = $this->compileInsert($values);

        $bindings = $this->cleanBindings($bindings);

        return $this->db->insert($sql, $bindings);
    }

    /**
     * Replace a new record into the database.
     *
     * @param  array  $values
     * @return bool
     */
    public function replace(array $values)
    {
        if (! is_array(reset($values))) {
            $values = array($values);
        } else {
            foreach ($values as $key => $value) {
                ksort($value);

                $values[$key] = $value;
            }
        }

        $bindings = array();

        foreach ($values as $record) {
            $bindings = array_merge($bindings, array_values($record));
        }

        $sql = $this->compileReplace($values);

        $bindings = $this->cleanBindings($bindings);

        return $this->db->insert($sql, $bindings);
    }

    /**
     * Insert a new record and get the value of the primary key.
     *
     * @param  array   $values
     * @return int
     */
    public function insertGetId(array $values)
    {
        $sql = $this->compileInsert($values);

        $values = $this->cleanBindings($values);

        $this->db->insert($sql, $values);

        $id = $this->db->getPdo()->lastInsertId();

        return is_numeric($id) ? (int) $id : $id;
    }

    /**
     * Update a record in the database.
     *
     * @param  array  $values
     * @return int
     */
    public function update(array $values)
    {
        $bindings = array_values(array_merge($values, $this->bindings));

        $sql = $this->compileUpdate($values);

        return $this->db->update($sql, $this->cleanBindings($bindings));
    }

    /**
     * Delete a record from the database.
     *
     * @return int
     */
    public function delete()
    {
        $sql = $this->compileDelete();

        return $this->db->delete($sql, $this->bindings);
    }

    /**
     * Run a truncate statement on the table.
     *
     * @return void
     */
    public function truncate()
    {
        foreach ($this->compileTruncate() as $sql => $bindings) {
            $this->db->statement($sql, $bindings);
        }
    }

    /**
     * Get a new instance of the query builder.
     *
     * @return \Database\Query
     */
    public function newQuery()
    {
        return new Query($this->db);
    }

    /**
     * Merge an array of bindings into our bindings.
     *
     * @param  \Database\Query  $query
     * @return \Database\Query
     */
    public function mergeBindings(Query $query)
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
        return array_values(array_filter($bindings, function($binding) {
            return true;
        }));
    }

    /**
     * Compile a select query into SQL.
     *
     * @param  \Database\Query  $query
     * @return string
     */
    public function compileSelect(Query $query)
    {
        if (is_null($query->columns)) {
            $query->columns = array('*');
        }

        return trim($this->concatenate($this->compileComponents($query)));
    }

    /**
     * Compile the components necessary for a select clause.
     *
     * @param  \Database\Query  $query
     * @return array
     */
    protected function compileComponents(Query $query)
    {
        $sql = array();

        $selectComponents = array(
            'aggregate',
            'columns',
            'from',
            'joins',
            'wheres',
            'groups',
            'havings',
            'orders',
            'limit',
            'unions',
            'offset'
        );

        foreach ($selectComponents as $component) {
            if (! is_null($query->{$component})) {
                $method = 'compile' .ucfirst($component);

                $sql[$component] = call_user_func(array($this, $method), $query, $query->{$component});
            }
        }

        return $sql;
    }

    /**
     * Compile an aggregated select clause.
     *
     * @param  \Database\Query  $query
     * @param  array  $aggregate
     * @return string
     */
    protected function compileAggregate(Query $query, $aggregate)
    {
        $column = $this->columnize($aggregate['columns']);

        if ($query->distinct && $column !== '*') {
            $column = 'DISTINCT ' .$column;
        }

        return 'SELECT ' .$aggregate['function'] .'(' .$column .') AS aggregate';
    }

    /**
     * Compile the "SELECT *" portion of the query.
     *
     * @param  \Database\Query  $query
     * @param  array  $columns
     * @return string
     */
    protected function compileColumns(Query $query, $columns)
    {
        if (is_null($query->aggregate)) {
            $select = $query->distinct ? 'SELECT DISTINCT ' : 'SELECT ';

            return $select .$this->columnize($columns);
        }

        return '';
    }

    /**
     * Compile the "FROM" portion of the query.
     *
     * @param  \Database\Query  $query
     * @param  string  $table
     * @return string
     */
    protected function compileFrom(Query $query, $table)
    {
        return 'FROM ' .$this->wrapTable($table);
    }

    /**
     * Compile the "JOIN" portions of the query.
     *
     * @param  \Database\Query  $query
     * @param  array  $joins
     * @return string
     */
    protected function compileJoins(Query $query, $joins)
    {
        $sql = array();

        foreach ($joins as $join) {
            $table = $this->wrapTable($join->table);

            $clauses = array();

            foreach ($join->clauses as $clause) {
                $clauses[] = $this->compileJoinConstraint($clause);
            }

            $clauses[0] = $this->removeLeadingBoolean($clauses[0]);

            $clauses = implode(' ', $clauses);

            $type = $join->type;

            $sql[] = "$type JOIN $table ON $clauses";
        }

        return implode(' ', $sql);
    }

    /**
     * Create a join clause constraint segment.
     *
     * @param  array   $clause
     * @return string
     */
    protected function compileJoinConstraint(array $clause)
    {
        $first = $this->wrap($clause['first']);

        $second = $clause['where'] ? '?' : $this->wrap($clause['second']);

        $boolean = strtoupper($clause['boolean']);

        return "$boolean $first {$clause['operator']} $second";
    }

    /**
     * Compile the "WHERE" portions of the query.
     *
     * @param  \Database\Query  $query
     * @return string
     */
    protected function compileWheres(Query $query)
    {
        $sql = array();

        if (is_null($query->wheres)) {
            return '';
        }

        foreach ($query->wheres as $where) {
            $method = "compileWhere{$where['type']}";

            $sql[] = strtoupper($where['boolean']) .' ' .call_user_func(array($this, $method), $where);
        }

        if (count($sql) > 0) {
            $sql = implode(' ', $sql);

            return 'WHERE ' .preg_replace('/AND |OR /', '', $sql, 1);
        }

        return '';
    }

    /**
     * Compile a nested where clause.
     *
     * @param  array  $where
     * @return string
     */
    protected function compileWhereNested($where)
    {
        $nested = $where['query'];

        return '(' .substr($this->compileWheres($nested), 6) .')';
    }

    /**
     * Compile a where condition with a sub-select.
     *
     * @param  array   $where
     * @return string
     */
    protected function compileWhereSub($where)
    {
        $select = $this->compileSelect($where['query']);

        return $this->wrap($where['column']) .' ' .$where['operator'] ." ($select)";
    }

    /**
     * Compile a basic where clause.
     *
     * @param  array  $where
     * @return string
     */
    protected function compileWhereBasic($where)
    {
        $value = $this->parameter($where['value']);

        return $this->wrap($where['column']) .' ' .$where['operator'] .' ' .$value;
    }

    /**
     * Compile a "BETWEEN" where clause.
     *
     * @param  array  $where
     * @return string
     */
    protected function compileWhereBetween($where)
    {
        $between = $where['not'] ? 'NOT BETWEEN' : 'BETWEEN';

        return $this->wrap($where['column']) .' ' .$between .' ? AND ?';
    }

    /**
     * Compile a "WHERE IN" clause.
     *
     * @param  array  $where
     * @return string
     */
    protected function compileWhereIn($where)
    {
        $values = $this->parameterize($where['values']);

        return $this->wrap($where['column']) .' IN (' .$values .')';
    }

    /**
     * Compile a "WHERE NOT IN" clause.
     *
     * @param  array  $where
     * @return string
     */
    protected function compileWhereNotIn($where)
    {
        $values = $this->parameterize($where['values']);

        return $this->wrap($where['column']) .' NOT IN (' .$values .')';
    }

    /**
     * Compile a where in sub-select clause.
     *
     * @param  array  $where
     * @return string
     */
    protected function compileWhereInSub($where)
    {
        $select = $this->compileSelect($where['query']);

        return $this->wrap($where['column']) .' IN (' .$select .')';
    }

    /**
     * Compile a where not in sub-select clause.
     *
     * @param  array  $where
     * @return string
     */
    protected function compileWhereNotInSub($where)
    {
        $select = $this->compileSelect($where['query']);

        return $this->wrap($where['column']) .' NOT IN (' .$select .')';
    }

    /**
     * Compile a "WHERE NULL" clause.
     *
     * @param  array  $where
     * @return string
     */
    protected function compileWhereNull($where)
    {
        return $this->wrap($where['column']) .' IS NULL';
    }

    /**
     * Compile a "WHERE NOT NULL" clause.
     *
     * @param  array  $where
     * @return string
     */
    protected function compileWhereNotNull($where)
    {
        return $this->wrap($where['column']) .' IS NOT NULL';
    }

    /**
     * Compile a raw "WHERE" clause.
     *
     * @param  \Database\Query  $query
     * @param  array  $where
     * @return string
     */
    protected function compileWhereRaw($where)
    {
        return $where['sql'];
    }

    /**
     * Compile the GROUP BY clause for a query.
     *
     * @param  Query   $query
     * @return string
     */
    protected function compileGroups(Query $query)
    {
        return 'GROUP BY '.$this->columnize($query->groups);
    }

    /**
     * Compile the "HAVING" portions of the query.
     *
     * @param  \Database\Query  $query
     * @param  array  $havings
     * @return string
     */
    protected function compileHavings(Query $query, $havings)
    {
        $sql = implode(' ', array_map(array($this, 'compileHaving'), $havings));

        return 'having '.preg_replace('/AND /', '', $sql, 1);
    }

    /**
     * Compile a single having clause.
     *
     * @param  array   $having
     * @return string
     */
    protected function compileHaving(array $having)
    {
        if ($having['type'] === 'Raw') {
            return $having['boolean'].' '.$having['sql'];
        }

        return $this->compileBasicHaving($having);
    }

    /**
     * Compile a basic having clause.
     *
     * @param  array   $having
     * @return string
     */
    protected function compileBasicHaving($having)
    {
        $column = $this->wrap($having['column']);

        $parameter = $this->parameter($having['value']);

        return 'AND ' .$column .' ' .$having['operator'] .' ' .$parameter;
    }

    /**
     * Compile the "ORDER BY" portions of the query.
     *
     * @param  \Database\Query  $query
     * @param  array  $orders
     * @return string
     */
    protected function compileOrders(Query $query, $orders)
    {
        $me = $this;

        return 'ORDER BY ' .implode(', ', array_map(function($order) use ($me) {
            if (isset($order['sql'])) return $order['sql'];

            return $me->wrap($order['column']).' '.$order['direction'];
        }, $orders));
    }

    /**
     * Compile the "LIMIT" portions of the query.
     *
     * @param  \Database\Query  $query
     * @param  int  $limit
     * @return string
     */
    protected function compileLimit(Query $query, $limit)
    {
        return 'LIMIT ' .(int) $limit;
    }

    /**
     * Compile the "OFFSET" portions of the query.
     *
     * @param  \Database\Query  $query
     * @param  int  $offset
     * @return string
     */
    protected function compileOffset(Query $query, $offset)
    {
        return 'OFFSET ' .(int) $offset;
    }

    /**
     * Compile the "UNION" queries attached to the main query.
     *
     * @param  \Database\Query  $query
     * @return string
     */
    protected function compileUnions(Query $query)
    {
        $sql = '';

        foreach ($query->unions as $union) {
            $sql .= $this->compileUnion($union);
        }

        return ltrim($sql);
    }

    /**
     * Compile a single "UNION" statement.
     *
     * @param  array  $union
     * @return string
     */
    protected function compileUnion(array $union)
    {
        $joiner = $union['all'] ? ' UNION ALL ' : ' UNION ';

        return $joiner .$union['query']->toSql();
    }

    /**
     * Compile an insert statement into SQL.
     *
     * @param  array  $values
     * @return string
     */
    public function compileInsert(array $values)
    {
        $table = $this->wrapTable($this->from);

        if (! is_array(reset($values))) {
            $values = array($values);
        }

        $columns = $this->columnize(array_keys(reset($values)));

        $parameters = $this->parameterize(reset($values));

        $value = array_fill(0, count($values), "($parameters)");

        $parameters = implode(', ', $value);

        return "INSERT INTO $table ($columns) VALUES $parameters";
    }

    /**
     * Compile an replace statement into SQL.
     *
     * @param  array  $values
     * @return string
     */
    public function compileReplace(array $values)
    {
        $table = $this->wrapTable($this->from);

        if (! is_array(reset($values))) {
            $values = array($values);
        }

        $columns = $this->columnize(array_keys(reset($values)));

        $parameters = $this->parameterize(reset($values));

        $value = array_fill(0, count($values), "($parameters)");

        $parameters = implode(', ', $value);

        return "REPLACE INTO $table ($columns) VALUES $parameters";
    }

    /**
     * Compile an update statement into SQL.
     *
     * @param  array  $values
     * @return string
     */
    public function compileUpdate($values)
    {
        $table = $this->wrapTable($this->from);

        $columns = array();

        foreach ($values as $key => $value) {
            $columns[] = $this->wrap($key) .' = ' .$this->parameter($value);
        }

        $columns = implode(', ', $columns);

        if (isset($this->joins)) {
            $joins = ' ' .$this->compileJoins($this, $this->joins);
        } else {
            $joins = '';
        }

        $where = $this->compileWheres($this);

        $sql = trim("UPDATE {$table}{$joins} SET $columns $where");

        if (isset($this->orders)) {
            $sql .= ' ' .$this->compileOrders($this, $this->orders);
        }

        if (isset($this->limit)) {
            $sql .= ' ' .$this->compileLimit($this, $this->limit);
        }

        return rtrim($sql);
    }

    /**
     * Compile a delete statement into SQL.
     *
     * @return string
     */
    public function compileDelete()
    {
        $table = $this->wrapTable($this->from);

        $where = is_array($this->wheres) ? $this->compileWheres($this) : '';

        $sql = trim("DELETE FROM $table " .$where);

        if (isset($this->limit)) {
            $sql .= ' ' .$this->compileLimit($this, $this->limit);
        }

        return rtrim($sql);
    }

    /**
     * Compile a truncate table statement into SQL.
     *
     * @param  \Database\Query  $query
     * @return array
     */
    public function compileTruncate()
    {
        return array('TRUNCATE ' .$this->wrapTable($this->from) => array());
    }

    /**
     * Wrap a table in keyword identifiers.
     *
     * @param  string  $table
     * @return string
     */
    public function wrapTable($table)
    {
        return $this->wrap($this->db->getTablePrefix() .$table);
    }

    /**
     * Wrap a value in keyword identifiers.
     *
     * @param  string  $value
     * @return string
     */
    public function wrap($value)
    {
        if (strpos(strtolower($value), ' as ') !== false) {
            $segments = explode(' ', $value);

            return $this->wrap($segments[0]) .' AS ' .$this->wrap($segments[2]);
        }

        $wrapped = array();

        $segments = explode('.', $value);

        foreach ($segments as $key => $segment) {
            if (($key == 0) && (count($segments) > 1)) {
                $wrapped[] = $this->wrapTable($segment);
            } else {
                $wrapped[] = $this->wrapValue($segment);
            }
        }

        return implode('.', $wrapped);
    }

    /**
     * Wrap a single string in keyword identifiers.
     *
     * @param  string  $value
     * @return string
     */
    protected function wrapValue($value)
    {
        return ($value !== '*') ? sprintf($this->wrapper, $value) : $value;
    }

    /**
     * Concatenate an array of segments, removing empties.
     *
     * @param  array   $segments
     * @return string
     */
    protected function concatenate($segments)
    {
        return implode(' ', array_filter($segments, function($value) {
            return (string) ($value !== '');
        }));
    }

    /**
     * Remove the leading boolean from a statement.
     *
     * @param  string  $value
     * @return string
     */
    protected function removeLeadingBoolean($value)
    {
        return preg_replace('/AND |OR /', '', $value, 1);
    }

    /**
     * Create query parameter place-holders for an array.
     *
     * @param  array   $values
     * @return string
     */
    public function parameterize(array $values)
    {
        return implode(', ', array_map(array($this, 'parameter'), $values));
    }

    /**
     * Get the appropriate query parameter place-holder for a value.
     *
     * @param  mixed   $value
     * @return string
     */
    public function parameter($value)
    {
        return ($value instanceof Expression) ? $value->get() : '?';
    }

    /**
     * Convert an array of column names into a delimited string.
     *
     * @param  array   $columns
     * @return string
     */
    public function columnize(array $columns)
    {
        return implode(', ', array_map(array($this, 'wrap'), $columns));
    }
}
