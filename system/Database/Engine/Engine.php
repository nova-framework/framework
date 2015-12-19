<?php


namespace Nova\Database\Engine;

/**
 * Interface Engine
 * @package Core\Database\Engine
 */
interface Engine
{
    /**
     * Get the name of the driver
     * @return string
     */
    public function getDriverName();

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
}
