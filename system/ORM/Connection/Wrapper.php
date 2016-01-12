<?php
/**
 * Connection Wrapper
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 11th, 2016
 */

namespace Nova\ORM\Connection;

use Nova\Database\Connection;
use Nova\Database\Manager as Database;


class Wrapper
{
    protected $db = null;

    protected $lastSqlQuery = null;


    public function __construct($linkName = 'default')
    {
        $this->db = Database::getConnection($linkName);
    }

    public function getLink()
    {
        return $this->db;
    }

    public function getTableFields($table)
    {
        return $this->db->getTableFields($table);
    }

}
