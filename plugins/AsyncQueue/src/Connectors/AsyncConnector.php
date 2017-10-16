<?php

namespace AcmeCorp\AsyncQueue\Connectors;

use Nova\Queue\Connectors\DatabaseConnector;
use Nova\Support\Arr;

use AcmeCorp\AsyncQueue\Queues\AsyncQueue;


class AsyncConnector extends DatabaseConnector
{

    /**
     * Establish a queue connection.
     *
     * @param array $config
     *
     * @return \Nova\Queue\Contracts\QueueInterface
     */
    public function connect(array $config)
    {
        $connection = Arr::get($config, 'connection');

        return new AsyncQueue(
            $this->connections->connection($connection),

            $config['table'],
            $config['queue'],

            Arr::get($config, 'expire', 60),
            Arr::get($config, 'binary', 'php'),
            Arr::get($config, 'binaryArgs', ''),
            Arr::get($config, 'connectionName', '')
        );
    }
}
