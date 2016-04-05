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

        $query = $this->query->getBaseQuery();

        //
        $data = $query->where($this->foreignKey, $id)->get();

        //
        $key = $this->related->getKeyName();

        $result = array();

        foreach ($data as $row) {
            $id = $row[$key];

            $result[$id] = $this->related->newFromBuilder($row);
        }

        $this->query = $this->related->newBuilder();

        return $result;
    }
}
