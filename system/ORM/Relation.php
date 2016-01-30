<?php
/**
 * BelongsTo
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 13th, 2016
 */

namespace Nova\ORM;

use Nova\ORM\Model;


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
     * @var \Nova\ORM\Model
     */
    protected $related;

    /**
     * The parent model instance.
     *
     * @var \Nova\ORM\Model
     */
    protected $parent;

    /**
     * The ORM query builder instance.
     *
     * @var \Nova\ORM\Builder
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
            throw new \Exception(__d('system', 'No valid Class is given: {0}', $className));
        }

        //
        $this->className = $className;

        // Setup the instance of Target Model.
        $this->related = new $className();

        // Setup the Parent Model
        $this->parent = $parent;

        // Setup the Query Builder
        $this->query = $this->related->newBuilder();
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
     * Get the underlying query for the relation.
     *
     * @return \Nova\ORM\Builder
     */
    public function getBuilder()
    {
        return $this->query;
    }

    /**
     * Get the parent model of the relation.
     *
     * @return \Nova\ORM\Model
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get the related model of the relation.
     *
     * @return \Nova\ORM\Model
     */
    public function getRelated()
    {
        return $this->related;
    }
}
