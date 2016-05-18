<?php
/**
 * DatabasePresenceVerifier - Helper for the verification of data presence in Database.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Validation;

use Database\Connection;
use Database\Query\Builder as QueryBuilder;


class DatabasePresenceVerifier
{
    /**
     * The database Connection instance.
     *
     * @var  \Database\Connection
     */
    protected $db;

    /**
     * Create a new Database Presence Verifier.
     *
     * @return void
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Count the number of objects in a collection having the given value.
     *
     * @param  string  $collection
     * @param  string  $column
     * @param  string  $value
     * @param  int     $excludeId
     * @param  string  $idColumn
     * @param  array   $extra
     * @return int
     */
    public function getCount($collection, $column, $value, $excludeId = null, $idColumn = null, array $extra = array())
    {
        $query = $this->table($collection)->where($column, '=', $value);

        if (! is_null($excludeId) && ($excludeId != 'NULL')) {
            $query->where($idColumn ?: 'id', '<>', $excludeId);
        }

        foreach ($extra as $key => $extraValue) {
            $this->addWhere($query, $key, $extraValue);
        }

        return $query->count();
    }

    /**
     * Count the number of objects in a collection with the given values.
     *
     * @param  string  $collection
     * @param  string  $column
     * @param  array   $values
     * @param  array   $extra
     * @return int
     */
    public function getMultiCount($collection, $column, array $values, array $extra = array())
    {
        $query = $this->table($collection)->whereIn($column, $values);

        foreach ($extra as $key => $extraValue) {
            $this->addWhere($query, $key, $extraValue);
        }

        return $query->count();
    }

    /**
     * Add a "WHERE" clause to the given query.
     *
     * @param  \Database\Query\Builder  $query
     * @param  string  $key
     * @param  string  $extraValue
     * @return void
     */
    protected function addWhere(QueryBuilder $query, $key, $extraValue)
    {
        if ($extraValue === 'NULL') {
            $query->whereNull($key);
        } elseif ($extraValue === 'NOT_NULL') {
            $query->whereNotNull($key);
        } else {
            $query->where($key, $extraValue);
        }
    }

    /**
     * Get a QueryBuilder instance for the given database Table.
     *
     * @param  string  $table
     * @return \Database\Query
     */
    protected function table($table)
    {
        return $this->db->table($table);
    }
}
