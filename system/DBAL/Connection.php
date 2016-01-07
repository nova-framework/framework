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

use Nova\DBAL\Logging\QueryCounter;

use PDO;


class Connection extends BaseConnection
{
    protected $defaultFetchType = 'array';


    /**
     * Constructor
     *
     * @param array $params
     * @param Driver $driver
     * @param Configuration $config
     * @param EventManager $eventManager
     */
    public function __construct(array $params, Driver $driver, Configuration $config = null, EventManager $eventManager = null)
    {
        if($config !== null) {
            // Setup our favorite Logger.
            $logger = new QueryCounter();

            $config->setSQLLogger($logger);
        }

        // Execute the parent Contructor.
        parent::__construct($params, $driver, $config, $eventManager);
    }

    public function setFetchType($fetchType)
    {
        $this->defaultFetchType = $fetchType;
    }

    public static function getFetchMode($fetchType, &$fetchClass = null) {
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

            // Check and setup the className.
            $classPath = str_replace('\\', '/', ltrim($fetchType, '\\'));

            if(! preg_match('#^App(?:/Modules/.+)?/Models/Entities/(.*)$#i', $classPath)) {
                throw new \Exception(__d('system', 'No valid Entity Name is given: {0}', $fetchType));
            }

            if(! class_exists($fetchType)) {
                throw new \Exception(__d('system', 'No valid Entity Class is given: {0}', $fetchType));
            }

            $fetchClass = $fetchType;
        }

        return $fetchMode;
    }

    public static function getParamTypes(array $params)
    {
        $result = array();

        foreach ($params as $key => $value) {
            if (is_integer($value)) {
                $result[$key] = PDO::PARAM_INT;
            }
            else if (is_bool($value)) {
                $result[$key] = PDO::PARAM_BOOL;
            }
            else if(is_null($value)) {
                $result[$key] = PDO::PARAM_NULL;
            }
            else {
                $result[$key] = PDO::PARAM_STR;
            }
        }

        return $result;
    }

    public function select($query, array $params = array(), $paramTypes = array(), $fetchType = null, $fetchAll = false)
    {
        // What fetch type? Use default if no return type is given in the call.
        $fetchType = ($fetchType !== null) ? $fetchType : $this->defaultFetchType;

        // Prepare the parameters.
        $className = null;

        $fetchMode = self::getFetchMode($fetchType, $className);

        // Prepare the parameter Types.
        $paramTypes = ! empty($paramTypes) ? $paramTypes : self::getParamTypes($params);

        //
        $this->connect();

        // Execute the current Query.
        $statement = $this->executeQuery($query, $params, $paramTypes);

        // Set the Statement's Fetch Mode
        $statement->setFetchMode($fetchMode, $className);

        // Fetch and return the result.
        if($fetchAll) {
            return $statement->fetchAll();
        }

        return $statement->fetch();
    }

    public function selectOne($query, array $params = array(), $paramTypes = array(), $fetchType = null)
    {
        return $this->select($query, $params, $paramTypes, $fetchType, false);
    }

    public function selectAll($query, array $params = array(), $paramTypes = array(), $fetchType = null)
    {
        return $this->select($query, $params, $paramTypes, $fetchType, true);
    }

    public function fetchObject($statement, array $params = array(), array $types = array())
    {
        $this->connect();

        return $this->executeQuery($statement, $params, $types)->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Fetch class
     *
     * @param string $statement
     * @param array $params
     * @param array $paramTypes
     * @param null|string $className
     * @param bool $fetchAll
     *
     * @return array|mixed
     *
     * @throws \Exception
     */
    public function fetchClass($statement, array $params = array(), array $paramTypes = array(), $className = null, $fetchAll = false)
    {
        if (($this->defaultFetchType != 'array') && ($this->defaultFetchType != 'object')) {
            $className = ($className !== null) ? $className : $this->defaultFetchType;
        }
        else if($className === null) {
            throw new \Exception(__d('system', 'No valid Entity Class is given'));
        }

        $this->connect();

        return $this->select($statement, $params, $paramTypes, $className, $fetchAll);
    }

    /**
     * Replaces a table row with specified data.
     *
     * Table expression and columns are not escaped and are not safe for user-input.
     *
     * @param string $table The expression of the table to insert data into, quoted or unquoted.
     * @param array  $data  An associative array containing column-value pairs.
     * @param array  $types Types of the inserted data.
     *
     * @return integer The number of affected rows.
     */
    public function replace($table, array $data, array $types = array())
    {
        $this->connect();

        if (empty($data)) {
            return $this->executeUpdate('REPLACE INTO ' . $table . ' ()' . ' VALUES ()');
        }

        return $this->executeUpdate(
            'REPLACE INTO ' . $table . ' (' . implode(', ', array_keys($data)) . ')' .
            ' VALUES (' . implode(', ', array_fill(0, count($data), '?')) . ')',
            array_values($data),
            is_string(key($types)) ? $this->extractTypeValues($data, $types) : $types
        );
    }

    /**
     * Get total executed Queries.
     *
     * @return int
     */
    public function getTotalQueries()
    {
        $logger = $this->getConfiguration()->getSQLLogger();

        if(! $logger instanceof QueryCounter) {
            // We can't get the number of queries.
            return 0;
        }

        return $logger->getNumQueries();
    }

}
