<?php
/**
 * Connection - Incapsulate and Extends the Doctrine DBAL's Connection.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 2th, 2016
 */

namespace Nova\DBAL;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Connection as BaseConnection;


class Connection extends BaseConnection
{
    protected $defaultFetchType = null;

    protected $queryCounter = 0;


    public function __construct(array $params, Driver $driver, Configuration $config = null, EventManager $eventManager = null)
    {
        parent::__construct($params, $driver, $config, $eventManager);
    }

    public function setFetchType($fetchType)
    {
        $this->defaultFetchType = $fetchType;
    }

    public function select($sql, array $params = array(), $types = array(), $fetchAll = false)
    {
        $statement = $this->executeQuery($sql, $params, $types);

        if($fetchAll) {
            return $statement->fetchAll();
        }

        return $statement->fetch();
    }

    public function executeQuery($query, array $params = array(), $types = array(), QueryCacheProfile $qcp = null)
    {
        $this->queryCounter++;

        return parent::executeQuery($query, $params, $types, $qcp);
    }

    public function executeUpdate($query, array $params = array(), array $types = array())
    {
        $this->queryCounter++;

        return parent::executeUpdate($query, $params, $types);
    }

    public function queryCounter()
    {
        return $this->queryCounter;
    }

}
