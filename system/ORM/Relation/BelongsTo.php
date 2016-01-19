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
    protected $otherId;


    public function __construct($className, Model $model, $otherKey = null)
    {
        parent::__construct($className);

        // Process the otherKey.
        if($otherKey === null) {
            $otherKey = $this->model->getForeignKey();
        }

        // The primaryKey is associated to target Model.
        $this->primaryKey = $this->model->getPrimaryKey();

        // The otherKey is associated to target Model.
        $this->otherId = $model->getAttribute($otherKey);
    }

    public function type()
    {
        return 'belongsTo';
    }

    public function get()
    {
        return $this->model->findBy($this->primaryKey, $this->otherId);
    }

}
