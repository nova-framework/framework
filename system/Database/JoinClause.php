<?php
/**
 * JoinClause - A Joining Clause helper class for the QueryBuilder.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Database;

use Database\Query;


class JoinClause
{
    /**
    * The QueryBuilder instance.
    *
    * @var \Database\Query
    */
    public $query;

    /**
    * The type of join being performed.
    *
    * @var string
    */
    public $type;

    /**
    * The table the join clause is joining to.
    *
    * @var string
    */
    public $table;

    /**
    * The "on" clauses for the join.
    *
    * @var array
    */
    public $clauses = array();

    /**
    * Create a new join clause instance.
    *
    * @param  \Database\Query  $query
    * @param  string  $type
    * @param  string  $table
    * @return void
    */
    public function __construct(Query $query, $type, $table)
    {
        $this->type  = $type;
        $this->query = $query;
        $this->table = $table;
    }

    /**
    * Add an "ON" clause to the join.
    *
    * @param  string  $first
    * @param  string  $operator
    * @param  string  $second
    * @param  string  $boolean
    * @param  bool  $where
    * @return \Database\JoinClause
    */
    public function on($first, $operator, $second, $boolean = 'and', $where = false)
    {
        $this->clauses[] = compact('first', 'operator', 'second', 'boolean', 'where');

        if ($where) {
            $this->query->addBinding($second);
        }

        return $this;
    }

    /**
    * Add an "OR ON" clause to the join.
    *
    * @param  string  $first
    * @param  string  $operator
    * @param  string  $second
    * @return \Database\JoinClause
    */
    public function orOn($first, $operator, $second)
    {
        return $this->on($first, $operator, $second, 'or');
    }

    /**
    * Add an "ON WHERE" clause to the join.
    *
    * @param  string  $first
    * @param  string  $operator
    * @param  string  $second
    * @param  string  $boolean
    * @return \Database\JoinClause
    */
    public function where($first, $operator, $second, $boolean = 'and')
    {
        return $this->on($first, $operator, $second, $boolean, true);
    }

    /**
    * Add an "OR ON WHERE" clause to the join.
    *
    * @param  string  $first
    * @param  string  $operator
    * @param  string  $second
    * @param  string  $boolean
    * @return \Database\JoinClause
    */
    public function orWhere($first, $operator, $second)
    {
        return $this->on($first, $operator, $second, 'or', true);
    }
}
