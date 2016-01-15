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


abstract class Relation extends BaseRelation
{
    protected $model;


    public function __construct($className)
    {
        parent::__construct();

        $className = sprintf('\\%s', ltrim($className, '\\'));

        if(! class_exists($className)) {
            throw new \Exception(__d('system', 'No valid Class is given: {0}', $className));
        }

        // Setup the instance of Target Model.
        $this->model = new $className();
    }

    abstract public function get();

}
