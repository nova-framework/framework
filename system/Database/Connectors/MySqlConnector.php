<?php
/**
 * MySqlConnector - A PDO based MySql Database Connector.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Database\Connectors;

use Database\Connectors\Connector;
use Database\Connectors\ConnectorInterface;

use PDO;


class MySqlConnector extends Connector implements ConnectorInterface
{
    /**
     * Establish a database connection.
     *
     * @param  array  $config
     * @return \PDO
     */
    public function connect(array $config)
    {
        $dsn = $this->getDsn($config);

        $options = $this->getOptions($config);

        $connection = $this->createConnection($dsn, $config, $options);

        //
        $collation = $config['collation'];

        $charset = $config['charset'];

        $names = "set names '$charset'".
            ( ! is_null($collation) ? " collate '$collation'" : '');

        $connection->prepare($names)->execute();

        if (isset($config['strict']) && $config['strict']) {
            $connection->prepare("set session sql_mode='STRICT_ALL_TABLES'")->execute();
        }

        return $connection;
    }

    /**
     * Create a DSN string from a configuration.
     *
     * @param  array   $config
     * @return string
     */
    protected function getDsn(array $config)
    {
        extract($config);

        $dsn = "mysql:host={$hostname};dbname={$database}";

        if (isset($config['port'])) {
            $dsn .= ";port={$port}";
        }
        
        if (isset($config['unix_socket'])) {
            $dsn .= ";unix_socket={$config['unix_socket']}";
        }

        return $dsn;
    }

}
