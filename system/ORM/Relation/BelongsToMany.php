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
use Nova\ORM\Pivot;


class BelongsToMany extends Relation
{
    protected $model;
    protected $pivot;

    protected $foreignKey;
    protected $otherKey;


    public function __construct($className, Model $model, $joinTable, $foreignKey, $otherKey = null, $otherKey = null, $otherKey, $otherKey = null)
    {
        if(! class_exists($className)) {
            throw new \Exception(__d('system', 'No valid Class is given: {0}', $className));
        }

        // Setup the instance of Target Model.
        $this->model = new $className();

        // Process the otherKey.
        if($otherKey === null) {
            $otherKey = $this->model->getForeignKey();
        }

        // Setup the Pivot.
        $this->pivot = new Pivot($joinTable, $foreignKey, $otherKey);

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
