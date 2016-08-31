<?php
/**
 * SQLiteConnector - A PDO based SQLite Database Connector.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Database\Connectors;

use Database\Connector;
use Database\ConnectorInterface;

use PDO;


class SQLiteConnector extends Connector implements ConnectorInterface
{

    /**
     * Establish a database connection.
     *
     * @param  array  $config
     * @return \PDO
     *
     * @throws \InvalidArgumentException
     */
    public function connect(array $config)
    {
        $options = $this->getOptions($config);

        if ($config['database'] == ':memory:') {
            return $this->createConnection('sqlite::memory:', $config, $options);
        }

        $path = realpath($config['database']);

        if ($path === false) {
            throw new \InvalidArgumentException("Database does not exist.");
        }

        return $this->createConnection("sqlite:{$path}", $config, $options);
    }

}
