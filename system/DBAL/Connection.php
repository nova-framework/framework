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
use PDO;

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

    public function select($sql, array $params = array(), $fetchAll = false, $fetchType = null, $paramTypes = array())
    {
        // Prepare the parameters.
        // What return type? Use default if no return type is given in the call.
        $fetchType = ($fetchType !== null) ? $fetchType : $this->defaltFetchType;

        $className = null;

        if($fetchType == 'array') {
            $fetchMode = PDO::FETCH_ASSOC;
        }
        else if($fetchType == 'object') {
            $fetchMode = PDO::FETCH_OBJ;
        }
        else {
            $fetchMode = PDO::FETCH_CLASS;

            // Check and setup the fetchClass.
            $className = $fetchType;

            $classPath = str_replace('\\', '/', ltrim($fetchType, '\\'));

            if(! preg_match('#^App(?:/Modules/.+)?/Models/Entities/(.*)$#i', $classPath)) {
                throw new \Exception("No valid Entity Name is given: " .$fetchType);
            }

            if(! class_exists($className)) {
                throw new \Exception("No valid Entity Class is given: " .$className);
            }
        }

        // Prepare the types.
        if(empty($paramTypes)) {
            foreach ($params as $key => $value) {
                if (is_integer($value)) {
                    $paramTypes[] = PDO::PARAM_INT;
                }
                else {
                    $paramTypes[] = PDO::PARAM_STR;
                }
            }
        }

        // Execute the current Query.
        $statement = $this->executeQuery($sql, $params, $paramTypes);

        // Fetch and return the result.
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

    public function getQueryCounter()
    {
        return $this->queryCounter;
    }

}
