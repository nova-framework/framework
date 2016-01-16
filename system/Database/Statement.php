<?php
/**
 * Statement
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 16th, 2016
 */

namespace Nova\Database;

use Nova\Database\Connection;

use \PDO;

/**
 * PDOStatement decorator that logs when a PDOStatement is executed.
 */
class Statement
{
    /**
     * The PDOStatement we decorate.
     */
    private $statement;

    /**
     * The Connection link.
     */
    private $connection;


    public function __construct(PDOStatement $statement, Connection $connection)
    {
        $this->statement  = $statement;
        $this->connection = $connection;
    }

    /**
    * When execute is called record the time it takes and
    * then log the query
    * @return PDO result set
    */
    public function execute()
    {
        $start = microtime(true);

        $result = $this->statement->execute();

        $this->connection->logQuery($this->statement->queryString, $start);

        return $result;
    }

    /**
    * Other than execute pass all other calls to the PDOStatement object
    * @param string $function_name
    * @param array $parameters arguments
    */
    public function __call($function_name, $parameters)
    {
        return call_user_func_array(array($this->statement, $function_name), $parameters);
    }

    public function __get($name)
    {
        return $this->statement->$name;
    }

}
