<?php
/**
 * BelongsToPivot
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 13th, 2016
 */

namespace Nova\ORM\Relation;

use Nova\Database\Connection;
use Nova\Database\Manager as Database;

use Nova\ORM\Model;


class BelongsToPivot
{
    protected $tableName;

    protected $db;

    protected $foreignKey;
    protected $otherKey;


    public function __construct($tableName, $foreignKey, $otherKey)
    {
        $this->tableName = $tableName;

        $this->db = Database::getConnection($connection);

        // The foreignKey is associated to host Model.
        $this->foreignKey = $foreignKey;

        // The otherKey is associated to target Model.
        $this->otherKey = $otherKey;
    }

    public function table()
    {
        return DB_PREFIX .$this->tableName;
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
