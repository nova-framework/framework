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
use Nova\ORM\Relation\JoiningPivot;


class BelongsToMany extends Relation
{
    protected $pivot;

    protected $foreignKey;
    protected $otherKey;


    public function __construct($className, Model $model, $joinTable, $foreignKey, $otherKey = null)
    {
        parent::__construct($className);

        // Process the otherKey.
        if($otherKey === null) {
            $otherKey = $this->model->getForeignKey();
        }

        // Setup the Pivot.
        $this->pivot = new JoiningPivot($joinTable, $foreignKey, $otherKey);

        // The primaryKey is associated to host Model.
        $this->foreignKey = $foreignKey;

        // The primaryKey is the foreignKey of the target Model.
        $this->otherKey = $otherKey;
    }

    public function get()
    {
        return false;
    }

    public function &pivot()
    {
        return $this->pivot;
    }

}
