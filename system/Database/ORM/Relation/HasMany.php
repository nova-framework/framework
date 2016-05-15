<?php
/**
 * HasMany
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Database\ORM\Relation;

use Database\ORM\Model;
use Database\ORM\Relation;


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
        $this->query = $this->related->newQuery();

        if($data === null) {
            return false;
        }

        //
        $key = $this->related->getKeyName();

        $result = array();

        foreach ($data as $row) {
            $id = $row[$key];

            $result[$id] = $this->related->newFromBuilder($row);
        }

        return $result;
    }
}
