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
        return $this->query->findManyBy($this->foreignKey, $this->parent->getKey());
    }

    /**
     * Handle dynamic method calls into the method.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if(in_array($method, $this->validQueryCalls) && method_exists($this->query, $method)) {
            $this->query = call_user_func_array(array($this->query, $method), $parameters);

            return $this;
        }
    }
}
