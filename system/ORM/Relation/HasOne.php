<?php
/**
 * HasOne
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 13th, 2016
 */

namespace Nova\ORM\Relation;

use Nova\ORM\Model;


class HasOne
{
    protected $model;
    protected $where;


    public function __construct($className, $foreignKey, $primaryKey)
    {
        if(! class_exists($className)) {
            throw new \Exception(__d('system', 'No valid Class is given: {0}', $className));
        }

        $this->$where = $where;

        //
        $this->model = new $className();
    }

    public function find()
    {
        return $this->model->findManyBy($this->foreignKey, $this->primaryKey);
    }
}
