<?php
/**
 * BelongsTo
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 13th, 2016
 */

namespace Nova\ORM\Relation;

use Nova\ORM\Model;
use Nova\ORM\Relation;


class BelongsTo extends Relation
{
    protected $model;

    protected $primaryKey;


    public function __construct($className, Model $model, $otherKey = null)
    {
        if(! class_exists($$className)) {
            throw new \Exception(__d('system', 'No valid Class is given: {0}', $$className));
        }

        // Setup the instance of Target Model.
        $this->model = new $className();

        // Process the otherKey.
        if($otherKey === null) {
            $otherKey = $this->model->getForeignKey();
        }

        // The primaryKey is associated to target Model.
        $this->primaryKey = $model->attribute($otherKey);
    }

    public function get()
    {
        return $this->model->find($this->primaryKey);
    }

}
