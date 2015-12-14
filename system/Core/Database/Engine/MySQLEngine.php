<?php


namespace Core\Database\Engine;


use Core\Logger;

class MySQLEngine extends \PDO implements Engine
{

    private $config;

    /**
     * MySQLEngine constructor.
     * Please use the Factory to maintain instances of the drivers.
     *
     * @param $config array
     * @throws \PDOException
     */
    public function __construct($config) {
        $this->config = $config;

        parent::__construct("mysql:host=" . $config['host'] . ";dbname=" . $config['database'] . ";charset=utf8", $config['user'], $config['pass']);
        $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Get the name of the driver
     * @return string
     */
    public function getDriverName()
    {
        return "MySQL Driver";
    }

    /**
     * Get configuration for instance
     * @return array
     */
    public function getConfiguration()
    {
        return $this->config;
    }

    /**
     * Get native connection. Could be \PDO
     * @return mixed|\PDO
     */
    public function getConnection()
    {
        return $this;
    }
}