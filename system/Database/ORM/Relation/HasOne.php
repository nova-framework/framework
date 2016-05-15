<?php
/**
 * HasOne
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Database\ORM\Relation;

use Database\ORM\Model;
use Database\ORM\Relation;


class HasOne extends Relation
{
    protected $foreignKey;


    public function __construct($className, Model $model, $foreignKey = null)
    {
        parent::__construct($className, $model);

        // The foreignKey is associated to host Model.
        if($foreignKey === null) {
            $this->foreignKey = $model->getForeignKey();
        } else {
            $this->foreignKey = $foreignKey;
        }
    }

    public function type()
    {
        return 'hasOne';
    }

    public function get()
    {
        $id = $this->parent->getKey();

        return $this->query->where($this->foreignKey, $id)->first();
    }
}
