<?php
/**
 * SQLiteGrammar - A simple SQLite Grammar for the QueryBuilder.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Database\Query\Grammars;

use Database\Query\Builder;
use Database\Query\Grammar;


class SQLiteGrammar extends Grammar
{
    /**
     * All of the available clause operators.
     *
     * @var array
     */
    protected $operators = array(
        '=', '<', '>', '<=', '>=', '<>', '!=',
        'like', 'not like', 'between', 'ilike',
        '&', '|', '<<', '>>',
    );

    /**
     * Compile an insert statement into SQL.
     *
     * @param  \Nova\Database\Query\Builder  $query
     * @param  array  $values
     * @return string
     */
    public function compileInsert(Builder $query, array $values)
    {
        $table = $this->wrapTable($query->from);

        if ( ! is_array(reset($values))) {
            $values = array($values);
        }

        if (count($values) == 1) {
            return parent::compileInsert($query, reset($values));
        }

        $names = $this->columnize(array_keys(reset($values)));

        $columns = array();

        foreach (array_keys(reset($values)) as $column) {
            $columns[] = '? as '.$this->wrap($column);
        }

        $columns = array_fill(0, count($values), implode(', ', $columns));

        return "INSERT INTO $table ($names) SELECT ".implode(' UNION SELECT ', $columns);
    }

    /**
     * Compile a truncate table statement into SQL.
     *
     * @param  \Database\Query\Builder  $query
     * @return array
     */
    public function compileTruncate(Builder $query)
    {
        $sql = array(
            'DELETE FROM sqlite_sequence WHERE name = ?'  => array($query->from),
            'DELETE FROM ' .$this->wrapTable($query->from) => array()
        );

        return $sql;
    }

    /**
     * Compile a "where day" clause.
     *
     * @param  \Nova\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereDay(Builder $query, $where)
    {
        return $this->dateBasedWhere('%d', $query, $where);
    }

    /**
     * Compile a "where month" clause.
     *
     * @param  \Nova\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereMonth(Builder $query, $where)
    {
        return $this->dateBasedWhere('%m', $query, $where);
    }

    /**
     * Compile a "where year" clause.
     *
     * @param  \Nova\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereYear(Builder $query, $where)
    {
        return $this->dateBasedWhere('%Y', $query, $where);
    }

    /**
     * Compile a date based where clause.
     *
     * @param  string  $type
     * @param  \Nova\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function dateBasedWhere($type, Builder $query, $where)
    {
        $value = str_pad($where['value'], 2, '0', STR_PAD_LEFT);

        $value = $this->parameter($value);

        return 'strftime(\'' .$type .'\', ' .$this->wrap($where['column']).') ' .$where['operator'] .' ' .$value;
    }
}
