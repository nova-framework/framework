<?php
/**
 * BelongsTo
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Database\ORM\Relation;

use Database\ORM\Model;
use Database\ORM\Relation;


class BelongsTo extends Relation
{
    protected $foreignKey;


    public function __construct($className, Model $model, $foreignKey = null)
    {
        parent::__construct($className, $model);

        // Process the foreignKey.
        if($foreignKey === null) {
            $this->foreignKey = $this->related->getForeignKey();
        } else {
            $this->foreignKey = $foreignKey;
        }
    }

    public function type()
    {
        return 'belongsTo';
    }

    public function get()
    {
        $id = $this->parent->getAttribute($this->foreignKey);

        return $this->query->where($this->related->getKeyName(), $id)->first();
    }

}
