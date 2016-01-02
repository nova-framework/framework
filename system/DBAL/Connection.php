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
    protected $defaultFetchType = 'array';

    protected $queryCounter = 0;


    public function __construct(array $params, Driver $driver, Configuration $config = null, EventManager $eventManager = null)
    {
        parent::__construct($params, $driver, $config, $eventManager);
    }

    public function setFetchType($fetchType)
    {
        $this->defaultFetchType = $fetchType;
    }

    public function select($sql, array $params = array(), $types = array(), $fetchAll = false, $returnType = null)
    {
        // Prepare the parameters.
        $className = null;

        if($returnType == 'array') {
            $fetchMode = \PDO::FETCH_ASSOC;
        }
        else if($returnType == 'object') {
            $fetchMode = \PDO::FETCH_OBJ;
        }
        else {
            $classPath = str_replace('\\', '/', ltrim($returnType, '\\'));

            if(! preg_match('#^App(?:/Modules/.+)?/Models/Entities/(.*)$#i', $classPath)) {
                throw new \Exception("No valid Entity Name is given: " .$returnType);
            }

            if(! class_exists($returnType)) {
                throw new \Exception("No valid Entity Class is given: " .$returnType);
            }

            $className = $returnType;

            $fetchMode = \PDO::FETCH_CLASS;
        }

        // Prepare the types.
        if(empty($types)) {
            foreach ($params as $key => $value) {
                if (is_integer($value)) {
                    $types[] = PDO::PARAM_INT;
                }
                else {
                    $types[] = PDO::PARAM_STR;
                }
            }
        }

        $statement = $this->executeQuery($sql, $params, $types);

        if($fetchAll) {
            return $statement->fetchAll($fetchMode, $className);
        }

        return $statement->fetch($fetchMode, $className);
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
