<?php
/**
 * Relation
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Database\ORM;

use Database\ORM\Model;


abstract class Relation
{
    /**
     * The related model class name.
     *
     * @var string
     */
    protected $className;

    /**
     * The related model instance.
     *
     * @var \Database\ORM\Model
     */
    protected $related;

    /**
     * The parent model instance.
     *
     * @var \Database\ORM\Model
     */
    protected $parent;

    /**
     * The ORM query builder instance.
     *
     * @var \Database\ORM\Query
     */
    protected $query;

    /**
     * The methods that should be returned from Query Builder.
     *
     * @var array
     */
    protected $passthru = array(
        'find',
        'findBy',
        'findMany',
        'findAll',
        'first',
        'insert',
        'insertIgnore',
        'replace',
        'update',
        'updateBy',
        'updateOrInsert',
        'delete',
        'deleteBy',
        'count',
        'countBy',
        'countAll',
        'isUnique',
        'query',
        'addTablePrefix'
    );


    public function __construct($className, Model $parent)
    {
        $className = sprintf('\\%s', ltrim($className, '\\'));

        if(! class_exists($className)) {
            throw new \Exception('No valid Class is given: ' .$className);
        }

        //
        $this->className = $className;

        // Setup the instance of Target Model.
        $this->related = new $className();

        // Setup the Parent Model
        $this->parent = $parent;

        // Setup the QueryBuilder
        $this->query = $this->related->newQuery();
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
        $result = call_user_func_array(array($this->query, $method), $parameters);

        return in_array($method, $this->passthru) ? $result : $this;
    }

    public function getClass()
    {
        return $this->className;
    }

    abstract public function get();

    /**
     * Get the underlying Query for the Relation.
     *
     * @return \Database\ORM\Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Get the parent Model of the Relation.
     *
     * @return \Database\ORM\Model
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get the related Model of the Relation.
     *
     * @return \Database\ORM\Model
     */
    public function getRelated()
    {
        return $this->related;
    }
}
