<?php
/**
 * Connection - Incapsulate and Extends the Doctrine DBAL's Connection.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 2th, 2016
 */

namespace Nova\DBAL;

use Doctrine\DBAL\Connection as DoctrineConnection;


class Connection extends DoctrineConnection
{
    protected $queryCounter = 0;

    // TBD


    
    public function queryCounter()
    {
        return $this->queryCounter;
    }

}
