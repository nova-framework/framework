<?php
/**
 * BelongsTo
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 13th, 2016
 */

namespace Nova\ORM;

use Nova\ORM\Base as BaseRelation;
use Nova\ORM\Model;


abstract class Relation extends BaseRelation
{
    protected $className;

    protected $related;

    protected $parent;


    public function __construct($className, Model $model)
    {
        parent::__construct();

        $className = sprintf('\\%s', ltrim($className, '\\'));

        if(! class_exists($className)) {
            throw new \Exception(__d('system', 'No valid Class is given: {0}', $className));
        }

        //
        $this->className = $className;

        // Setup the instance of Target Model.
        $this->related = new $className();

        // Setup the Parent Model
        $this->parent = $model;
    }

    public function getClassName()
    {
        return $this->className;
    }

    abstract public function get();

}
