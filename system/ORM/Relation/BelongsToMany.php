<?php
/**
 * BelongsToMany
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 13th, 2016
 */

namespace Nova\ORM\Relation;

use Nova\ORM\Model;
use Nova\ORM\Relation;
use Nova\ORM\Relation\Joining\Pivot as JoiningPivot;


class BelongsToMany extends Relation
{
    use \Nova\ORM\Query\Builder;

    //
    protected $pivot;

    protected $foreignKey;

    protected $otherKey;
    protected $otherId;


    public function __construct($className, Model $model, $joinTable, $foreignKey, $otherKey = null)
    {
        parent::__construct($className);

        // Process the otherKey.
        if($otherKey === null) {
            $otherKey = $this->model->getForeignKey();
        }

        // The primaryKey is associated to host Model.
        $this->foreignKey = $otherKey;

        // The primaryKey is the foreignKey of the target Model.
        $this->otherKey = $foreignKey;

        $this->otherId = $model->getPrimaryKey();

        // Setup the Joining Pivot.
        $this->pivot = new JoiningPivot($joinTable, $foreignKey, $otherKey, $this->otherId);
    }

    public function get()
    {
        $table = $this->pivot->table();

        $order  = $this->getOrder();
        $limit  = $this->getLimit();
        $offset = $this->getOffset();

        // Execute the Query.
        $result = $this->model
            ->where($this->wheres())
            ->orderBy($order)
            ->limit($limit)
            ->offset($offset)
            ->fetchWithPivot($table, $this->foreignKey, $this->otherKey, $this->otherId);

        $this->resetState();

        return $result;
    }

    public function &pivot()
    {
        return $this->pivot;
    }

}
