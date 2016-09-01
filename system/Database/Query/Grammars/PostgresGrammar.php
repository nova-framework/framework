<?php
/**
 * PostgresGrammar - A simple PostgresSQL Grammar for the QueryBuilder.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Database\Query\Grammars;

use Database\Query\Builder;
use Database\Query\Grammar;


class PostgresGrammar extends Grammar
{
    /**
     * All of the available clause operators.
     *
     * @var array
     */
    protected $operators = array(
        '=', '<', '>', '<=', '>=', '<>', '!=',
        'like', 'not like', 'between', 'ilike',
        '&', '|', '#', '<<', '>>',
    );


    /**
     * Compile the lock into SQL.
     *
     * @param  \Database\Query\Builder  $query
     * @param  bool|string  $value
     * @return string
     */
    protected function compileLock(Builder $query, $value)
    {
        if (is_string($value)) return $value;

        return $value ? 'for update' : 'for share';
    }

    /**
     * Compile an update statement into SQL.
     *
     * @param  \Database\Query\Builder  $query
     * @param  array  $values
     * @return string
     */
    public function compileUpdate(Builder $query, $values)
    {
        $table = $this->wrapTable($query->from);

        $columns = $this->compileUpdateColumns($values);

        $from = $this->compileUpdateFrom($query);

        $where = $this->compileUpdateWheres($query);

        return trim("update {$table} set {$columns}{$from} $where");
    }

    /**
     * Compile the columns for the update statement.
     *
     * @param  array   $values
     * @return string
     */
    protected function compileUpdateColumns($values)
    {
        $columns = array();

        foreach ($values as $key => $value) {
            $columns[] = $this->wrap($key).' = '.$this->parameter($value);
        }

        return implode(', ', $columns);
    }

    /**
     * Compile the "from" clause for an update with a join.
     *
     * @param  \Database\Query\Builder  $query
     * @return string
     */
    protected function compileUpdateFrom(Builder $query)
    {
        if ( ! isset($query->joins)) return '';

        $froms = array();

        foreach ($query->joins as $join) {
            $froms[] = $this->wrapTable($join->table);
        }

        if (count($froms) > 0) return ' from '.implode(', ', $froms);
    }

    /**
     * Compile the additional where clauses for updates with joins.
     *
     * @param  \Database\Query\Builder  $query
     * @return string
     */
    protected function compileUpdateWheres(Builder $query)
    {
        $baseWhere = $this->compileWheres($query);

        if ( ! isset($query->joins)) return $baseWhere;

        $joinWhere = $this->compileUpdateJoinWheres($query);

        if (trim($baseWhere) == '') {
            return 'where '.$this->removeLeadingBoolean($joinWhere);
        }

        return $baseWhere .' ' .$joinWhere;
    }

    /**
     * Compile the "join" clauses for an update.
     *
     * @param  \Database\Query\Builder  $query
     * @return string
     */
    protected function compileUpdateJoinWheres(Builder $query)
    {
        $joinWheres = array();

        foreach ($query->joins as $join) {
            foreach ($join->clauses as $clause) {
                $joinWheres[] = $this->compileJoinConstraint($clause);
            }
        }

        return implode(' ', $joinWheres);
    }

    /**
     * Compile an insert and get ID statement into SQL.
     *
     * @param  \Database\Query\Builder  $query
     * @param  array   $values
     * @param  string  $sequence
     * @return string
     */
    public function compileInsertGetId(Builder $query, $values, $sequence)
    {
        if (is_null($sequence)) $sequence = 'id';

        return $this->compileInsert($query, $values).' returning '.$this->wrap($sequence);
    }

    /**
     * Compile a truncate table statement into SQL.
     *
     * @param  \Database\Query\Builder  $query
     * @return array
     */
    public function compileTruncate(Builder $query)
    {
        return array('truncate ' .$this->wrapTable($query->from) .' restart identity' => array());
    }
}

