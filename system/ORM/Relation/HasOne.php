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
use Nova\ORM\Relation;


class HasOne extends Relation
{
    protected $model;

    protected $foreignKey;
    protected $primaryKey;


    public function __construct($className, Model $model, $foreignKey)
    {
        if(! class_exists($className)) {
            throw new \Exception(__d('system', 'No valid Class is given: {0}', $className));
        }

        // Setup the instance of Target Model.
        $this->model = new $className();

        // The foreignKey is associated to host Model.
        $this->foreignKey = $foreignKey;

        // The primaryKey is associated to this Model.
        $this->primaryKey = $model->getPrimaryKey();
    }

    public function get()
    {
        return $this->model->findBy($this->foreignKey, $this->primaryKey);
    }
}
