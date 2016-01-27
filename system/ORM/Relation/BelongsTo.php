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
    protected $primaryKey;

    protected $primaryId;


    public function __construct($className, Model $model, $otherKey = null)
    {
        parent::__construct($className, $model);

        // Process the otherKey.
        if($otherKey === null) {
            $otherKey = $this->related->getForeignKey();
        }

        // The primaryKey is associated to target Model.
        $this->primaryKey = $this->related->getKeyName();

        // The otherKey is associated to target Model.
        $this->primaryId = $model->getAttribute($otherKey);
    }

    public function type()
    {
        return 'belongsTo';
    }

    public function get()
    {
        return $this->query->findBy($this->primaryKey, $this->primaryId);
    }

}
