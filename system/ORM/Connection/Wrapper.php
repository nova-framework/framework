<?php
/**
 * Connection Wrapper
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 11th, 2016
 */

namespace Nova\ORM\Connection;

use Nova\ORM\Connection\Adapter;


class Wrapper
{
    protected $adapter;


    public function __construct()
    {
    }

    public function getTableFields()
    {
        // TBD
        
        return array();
    }

}
