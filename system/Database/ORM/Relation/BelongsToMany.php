<?php
/**
 * BelongsToMany
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Database\ORM\Relation;

use Database\Connection;

use Database\ORM\Model;
use Database\ORM\Relation;
use Database\ORM\Relation\Pivot as RelationPivot;


class BelongsToMany extends Relation
{
    protected $table;
    protected $pivot;

    protected $foreignKey;
    protected $otherKey;


    public function __construct($className, Model $model, $joinTable, $foreignKey, $otherKey = null)
    {
        parent::__construct($className, $model);

        // Process foreignKey.
        if($otherKey === null) {
            $otherKey = $this->related->getForeignKey();
        }

        // The foreignKey is associated to target Model.
        $this->foreignKey = $otherKey;

        // The otherKey is the foreignKey of the host Model.
        if($foreignKey === null) {
            $foreignKey = $model->getForeignKey();
        }

        $this->otherKey = $foreignKey;

        // Setup the pivot Table.
        $this->table = $joinTable;

        // Setup the Joining Pivot.
        $attributes = array($this->otherKey => $model->getKey());

        $this->pivot = $this->newPivot($attributes);
    }

    public function type()
    {
        return 'belongsToMany';
    }

    public function &pivot()
    {
        return $this->pivot;
    }

    /**
     * Create a new pivot model instance.
     *
     * @param  array  $attributes
     * @param  bool   $exists
     * @return \Database\ORM\Relation\Pivot
     */
    public function newPivot(array $attributes = array(), $exists = false)
    {
        $pivot = $this->related->newPivot($this->parent, $attributes, $this->table, $exists);

        return $pivot->setPivotKeys($this->foreignKey, $this->otherKey);
    }

    /**
     * Create a new existing pivot model instance.
     *
     * @param  array  $attributes
     * @return \Database\ORM\Relation\Pivot
     */
    public function newExistingPivot(array $attributes = array())
    {
        return $this->newPivot($attributes, true);
    }

    public function get()
    {
        $table = $this->related->getTable();

        $pivotTable = $this->pivot->getTable();

        //
        $query = $this->query->getBaseQuery();

        $tableKey = $query->addTablePrefix($table .'.' .$this->related->getKeyName());
        $pivotKey = $query->addTablePrefix($pivotTable .'.' .$this->foreignKey);

        // Get the pivot's Raw command.
        $pivotRaw = $query->raw($tableKey .' = ' .$pivotKey);

        $data = $query
            ->from($pivotTable)
            ->where($pivotRaw)
            ->where($pivotTable .'.' .$this->otherKey, $this->parent->getKey())
            ->select($table .'.*')
            ->get();

        //
        $this->query = $this->related->newBuilder();

        if($data === null) {
            return false;
        }

        //
        $key = $this->related->getKeyName();

        $result = array();

        foreach($data as $row) {
            $id = $row[$key];

            $result[$id] = $this->related->newFromBuilder($row);
        }

        return $result;
    }

    public function attach($id, array $attributes = array())
    {
        $query = $this->pivot->newBuilder();

        $otherId = $this->parent->getKey();

        // Prepare the data.
        $data = array(
            $this->foreignKey => $id,
            $this->otherKey => $otherId
        );

        if(! empty($attributes)) {
            $data = array_merge($data, $attributes);
        }

        return $query->insert($data);
    }

    public function dettach($ids = null)
    {
        $query = $this->pivot->newBuilder();

        $otherId = $this->parent->getKey();

        if(is_array($ids)) {
            $query = $query->whereIn($this->foreignKey, $ids);
        } else if(! is_null($ids)) {
            $query = $query->where($this->foreignKey, $ids);
        }

        return $query->deleteBy($this->otherKey, $otherId);
    }

    public function sync($ids, $detaching = true)
    {
        $changes = array(
            'attached' => array(),
            'detached' => array(),
            'updated'  => array(),
        );

        $current = $this->pivot->relatedIds();

        $records = $this->formatSyncList($ids);

        $detach = array_diff($current, array_keys($records));

        if ($detaching && (count($detach) > 0)) {
            $this->dettach($detach);

            $changes['detached'] = (array) array_map(function ($id) {
                return is_numeric($id) ? (int) $id : (string) $id;
            }, $detach);
        }

        $changes = array_merge(
            $changes, $this->attachNew($records, $current)
        );

        return $changes;
    }

    /**
     * Format the sync list so that it is keyed by ID.
     *
     * @param  array  $records
     * @return array
     */
    protected function formatSyncList(array $records)
    {
        $results = array();

        foreach ($records as $id => $attributes) {
            if (! is_array($attributes)) {
                list($id, $attributes) = array($attributes, array());
            }

            $results[$id] = $attributes;
        }

        return $results;
    }

    protected function attachNew(array $records, array $current)
    {
        $changes = array(
            'attached' => array(),
            'updated' => array()
        );

        foreach ($records as $id => $attributes) {
            if (! in_array($id, $current)) {
                $this->attach($id, $attributes);

                $changes['attached'][] = is_numeric($id) ? (int) $id : (string) $id;
            } else if ((count($attributes) > 0) && $this->updateExistingPivot($id, $attributes)) {
                $changes['updated'][] = is_numeric($id) ? (int) $id : (string) $id;
            }
        }

        return $changes;
    }

    protected function updateExistingPivot($id, array $attributes)
    {
        $query = $this->pivot->newBuilder();

        $otherId = $this->parent->getKey();

        //
        return $query->where($this->foreignKey, $id)->where($this->otherKey, $otherId)->update($attributes);
    }

}
