<?php

namespace Database\Query\Grammars;

use Database\Query\Builder;
use Database\Query\Grammar;


class SqlServerGrammar extends Grammar
{
    /**
     * All of the available clause operators.
     *
     * @var array
     */
    protected $operators = array(
        '=', '<', '>', '<=', '>=', '!<', '!>', '<>', '!=',
        'like', 'not like', 'between', 'ilike',
        '&', '&=', '|', '|=', '^', '^=',
    );

    /**
     * Compile a select query into SQL.
     *
     * @param  \Database\Query\Builder
     * @return string
     */
    public function compileSelect(Builder $query)
    {
        $components = $this->compileComponents($query);

        if ($query->offset > 0) {
            return $this->compileAnsiOffset($query, $components);
        }

        return $this->concatenate($components);
    }

    /**
     * Compile the "select *" portion of the query.
     *
     * @param  \Database\Query\Builder  $query
     * @param  array  $columns
     * @return string
     */
    protected function compileColumns(Builder $query, $columns)
    {
        if ( ! is_null($query->aggregate)) return;

        $select = $query->distinct ? 'SELECT DISTINCT ' : 'SELECT ';

        if (($query->limit > 0) && ($query->offset <= 0)) {
            $select .= 'top '.$query->limit.' ';
        }

        return $select.$this->columnize($columns);
    }

    /**
     * Compile the "from" portion of the query.
     *
     * @param  \Database\Query\Builder  $query
     * @param  string  $table
     * @return string
     */
    protected function compileFrom(Builder $query, $table)
    {
        $from = parent::compileFrom($query, $table);

        if (is_string($query->lock)) return $from.' '.$query->lock;

        if ( ! is_null($query->lock)) {
            return $from.' with(rowlock,'.($query->lock ? 'updlock,' : '').'holdlock)';
        }

        return $from;
    }

    /**
     * Create a full ANSI offset clause for the query.
     *
     * @param  \Database\Query\Builder  $query
     * @param  array  $components
     * @return string
     */
    protected function compileAnsiOffset(Builder $query, $components)
    {
        if ( ! isset($components['orders'])) {
            $components['orders'] = 'ORDER BY (SELECT 0)';
        }

        $orderings = $components['orders'];

        $components['columns'] .= $this->compileOver($orderings);

        unset($components['orders']);

        $constraint = $this->compileRowConstraint($query);

        $sql = $this->concatenate($components);

        return $this->compileTableExpression($sql, $constraint);
    }

    /**
     * Compile the over statement for a table expression.
     *
     * @param  string  $orderings
     * @return string
     */
    protected function compileOver($orderings)
    {
        return ", row_number() OVER ({$orderings}) AS row_num";
    }

    /**
     * Compile the limit / offset row constraint for a query.
     *
     * @param  \Database\Query\Builder  $query
     * @return string
     */
    protected function compileRowConstraint($query)
    {
        $start = $query->offset + 1;

        if ($query->limit > 0)
        {
            $finish = $query->offset + $query->limit;

            return "BETWEEN {$start} AND {$finish}";
        }

        return ">= {$start}";
    }

    /**
     * Compile a common table expression for a query.
     *
     * @param  string  $sql
     * @param  string  $constraint
     * @return string
     */
    protected function compileTableExpression($sql, $constraint)
    {
        return "SELECT * FROM ({$sql}) AS temp_table WHERE row_num {$constraint}";
    }

    /**
     * Compile the "limit" portions of the query.
     *
     * @param  \Database\Query\Builder  $query
     * @param  int  $limit
     * @return string
     */
    protected function compileLimit(Builder $query, $limit)
    {
        return '';
    }

    /**
     * Compile the "offset" portions of the query.
     *
     * @param  \Database\Query\Builder  $query
     * @param  int  $offset
     * @return string
     */
    protected function compileOffset(Builder $query, $offset)
    {
        return '';
    }

    /**
     * Compile a truncate table statement into SQL.
     *
     * @param  \Database\Query\Builder  $query
     * @return array
     */
    public function compileTruncate(Builder $query)
    {
        return array('TRUNCATE TABLE ' .$this->wrapTable($query->from) => array());
    }

    /**
     * Get the format for database stored dates.
     *
     * @return string
     */
    public function getDateFormat()
    {
        return 'Y-m-d H:i:s.000';
    }

    /**
     * Wrap a single string in keyword identifiers.
     *
     * @param  string  $value
     * @return string
     */
    protected function wrapValue($value)
    {
        if ($value === '*') return $value;

        return '[' .str_replace(']', ']]', $value).']';
    }

}
