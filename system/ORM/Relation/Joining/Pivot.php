<?php
/**
 * BelongsToPivot
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 13th, 2016
 */

namespace Nova\ORM\Relation\Joining;

use Nova\Database\Connection;
use Nova\Database\Manager as Database;

use Nova\ORM\Engine;
use Nova\ORM\Model;


class Pivot extends Engine
{
    protected $otherKey;


    public function __construct($tableName, $primaryKey, $otherKey)
    {
        $this->tableName = $tableName;

        $this->primaryKey = $foreignKey;

        // Execute the parent Constructor.
        parent::__construct();

        // The otherKey is associated to target Model.
        $this->otherKey = $otherKey;
    }

    public function attach($params)
    {
        return false;
    }

    public function dettach($params)
    {
        return false;
    }

    public function sync($params)
    {
        return false;
    }

}
