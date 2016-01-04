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

    /**
     * Constructor
     */
    public function __construct(array $params, Driver $driver, Configuration $config = null, EventManager $eventManager = null)
    {
        parent::__construct($params, $driver, $config, $eventManager);
    }

    public function setFetchType($fetchType)
    {
        $this->defaultFetchType = $fetchType;
    }

    public function select($sql, array $params = array(), $paramTypes = array(), $fetchType = null, $fetchAll = false)
    {
        // What fetch type? Use default if no return type is given in the call.
        $fetchType = ($fetchType !== null) ? $fetchType : $this->defaultFetchType;

        // Prepare the parameters.
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
                throw new \Exception("No valid Entity Name is given: " .$className);
            }

            if(! class_exists($className)) {
                throw new \Exception("No valid Entity Class is given: " .$className);
            }
        }

        // Prepare the parameter Types.
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

        if(($fetchMode == PDO::FETCH_CLASS) && ($className !== null)) {
            $statement->setFetchMode($fetchMode, $className);

            return $statement->fetch();
        }

        return $statement->fetch($fetchMode);
    }

    public function selectAll($sql, array $params = array(), $paramTypes = array(), $fetchType = null)
    {
        return $this->select($sql, $params, $paramTypes, $fetchType, true);
    }

    public function fetchObject($statement, array $params = array(), array $types = array())
    {
        return $this->executeQuery($statement, $params, $types)->fetch(PDO::FETCH_OBJ);
    }

    public function fetchClass($statement, array $params = array(), array $paramTypes = array(), $className = null)
    {
        if (($this->defaultFetchType != 'array') && ($this->defaultFetchType != 'object')) {
            $className = ($className !== null) ? $className : $this->defaultFetchType;
        }
        else if($className === null) {
            throw new \Exception("No valid Entity Class is given");
        }

        return $this->select($statement, $params, $paramTypes, $className);
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
