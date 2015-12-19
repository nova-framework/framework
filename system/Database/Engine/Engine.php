<?php


namespace Nova\Database\Engine;

/**
 * Interface Engine
 * @package Nova\Database\Engine
 */
interface Engine
{
    /**
     * Get the name of the driver
     * @return string
     */
    public function getDriverName();

    /**
     * Get driver code, used in config as driver string.
     * @return string
     */
    public function getDriverCode();

    /**
     * Get configuration for instance
     * @return array
     */
    public function getConfiguration();

    /**
     * Get native connection. Could be \PDO
     * @return mixed|\PDO
     */
    public function getConnection();

    /**
     * Basic execute statement. Only for small queries with no binding parameters
     *
     * @param $sql
     * @return mixed
     */
    public function executeSimpleQuery($sql);
}
