<?php
/**
 * HasMany
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 13th, 2016
 */

namespace Nova\ORM\Relation;

use Nova\ORM\Model;
use Nova\ORM\Relation;


class HasMany extends Relation
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
        return 'hasMany';
    }

    public function get()
    {
        $id = $this->parent->getKey();

        $models = $this->query->findAll($this->foreignKey, $id);

        $result = array();

        foreach ($models as $model) {
            $key = $model->getKey();
            
            $result[$key] = $model;
        }

        $this->query = $this->related->newBuilder();

        return $result;
    }
}
