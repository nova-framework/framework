<?php
/**
 * DatabaseLoader - Implements a Configuration Loader for Database storage.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date April 12th, 2016
 */

namespace Config;

use Database\Connection;


class DatabaseLoader implements LoaderInterface
{
    /**
     * The Database Connection instance.
     *
     * @var \Database\Connection
     */
    protected $connection;

    /**
     * The Database Table.
     *
     * @var string
     */
    protected $table = 'options';

    /**
     * Create a new fileloader instance.
     *
     * @return void
     */
    function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Load the Configuration Group for the key.
     *
     * @param    string     $group
     * @return     array
     */
    public function load($group)
    {
        $items = array();

        // The current Group's data is not cached.
        $results = $this->newQuery()
            ->where('group', $group)
            ->get(array('item', 'value'));

        foreach ($results as $result) {
            $result = (array) $result;

            // Insert the option on list.
            $key = $result['item'];

            // Process the (optional) JSON encoding.
            $value = json_decode($result['value'], true);

            if (json_last_error() === JSON_ERROR_NONE) {
                // A valid JSON data there.
                $items[$key] = $value;
            } else {
                $items[$key] = $result['value'];
            }
        }

        return $items;
    }

    /**
     * Set a given configuration value.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function set($key, $value)
    {
        @list($group, $item) = $this->parseKey($key);

        // Update the information on Database.
        if (empty($item)) {
            foreach ($value as $item => $val) {
                $this->update($group, $item, $val);
            }
        } else {
            $this->update($group, $item, $value);
        }
    }

    /**
     * Update or Insert the given configuration value.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    protected function update($group, $item, $value)
    {
        $value = is_string($value) ? $value : json_encode($value);

        $id = $this->newQuery()
            ->where('group', $group)
            ->where('item', $item)
            ->pluck('id');

        if (is_null($id)) {
            $this->newQuery()
                ->insert(compact('group', 'item', 'value'));
        } else {
            $this->newQuery()->where('id', $id)
                ->limit(1)
                ->update(compact('value'));
        }
    }

    /**
     * Parse a key into group, and item.
     *
     * @param  string  $key
     * @return array
     */
    protected function parseKey($key)
    {
        $segments = explode('.', $key);

        $group = $segments[0];

        unset($segments[0]);

        $segments = implode('.', $segments);

        return array($group, $segments);
    }

    /**
     * Set the database table.
     *
     * @param string
     * @return void
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * Create a new database query
     *
     * @return \Database\Query
     */
    public function newQuery()
    {
        return $this->connection->table($this->table);
    }
}
