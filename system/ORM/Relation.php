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

    /**
     * Handle dynamic method calls into the method.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $validMethod = false;

        switch($method) {
            case 'where':
            case 'limit':
            case 'offset':
            case 'orderBy':
                $validMethod = true;

                break;
            default:
                break;
        }

        if ($validMethod) {
            return call_user_func_array(array($this, $method), $parameters);
        }
    }
    
    abstract public function get();

}
