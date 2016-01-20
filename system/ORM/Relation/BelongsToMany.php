<?php
/**
 * BelongsToMany
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 13th, 2016
 */

namespace Nova\ORM\Relation;

use Nova\Database\Connection;
use Nova\Database\Manager as Database;

use Nova\ORM\Model;
use Nova\ORM\Relation;
use Nova\ORM\Relation\Pivot as RelationPivot;


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
     * @return \Nova\ORM\Relation\Pivot
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
     * @return \Nova\ORM\Relation\Pivot
     */
    public function newExistingPivot(array $attributes = array())
    {
        return $this->newPivot($attributes, true);
    }

    public function get()
    {
        $table = $this->pivot->table();

        $order  = $this->getOrder();
        $limit  = $this->getLimit();
        $offset = $this->getOffset();

        // Execute the Query.
        $result = $this->related
            ->where($this->wheres())
            ->orderBy($order)
            ->limit($limit)
            ->offset($offset)
            ->fetchWithPivot($table, $this->foreignKey, $this->otherKey, $this->parent->getKey());

        $this->resetState();

        return $result;
    }

    public function attach($id, array $attributes = array())
    {
        $table = $this->pivot->table();

        $data = array(
            $this->foreignKey => $id,
            $this->otherKey => $this->parent->getKey()
        );

        if(! empty($attributes)) {
            $data = array_merge($attributes, $data);
        }

        return $this->pivot->insert($data);
    }

    public function dettach($ids = null)
    {
        $table = $this->pivot->table();

        $foreignKey = $this->foreignKey;

        $where = array($this->otherKey => $this->parent->getKey());

        if(! is_null($ids)) {
            $where[$foreignKey] = $ids;
        }

        return $this->pivot->deleteBy($where);
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
        $table = $this->pivot->table();

        $connection = $this->model->getConnection();

        $where = array(
            $this->foreignKey => $id,
            $this->otherKey => $this->parent->getKey()
        );

        return $this->pivot->update($attributes, $where);
    }

}
