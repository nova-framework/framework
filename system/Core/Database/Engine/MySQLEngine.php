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
     */
    public function __construct($config) {
        $this->config = $config;

        try {
            parent::__construct("mysql:host=" . $config['host'] . ";dbname=" . $config['database'] . ";charset=utf8", $config['user'], $config['pass']);
            $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            //in the event of an error record the error to the error log
            Logger::newMessage($e);
            Logger::customErrorMsg();

            throw $e;
        }
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