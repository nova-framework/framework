<?php

namespace Database\Connections;

use Database\Connection;
use Database\Query\Grammars\PostgresGrammar as QueryGrammar;
use Database\Query\Processors\PostgresProcessor as QueryProcessor;


class PostgresConnection extends Connection
{
    /**
     * Get the default query grammar instance.
     *
     * @return \Database\Query\Grammars\PostgresGrammar
     */
    protected function getDefaultQueryGrammar()
    {
        return $this->withTablePrefix(new QueryGrammar());
    }

    /**
     * Get the default post processor instance.
     *
     * @return \Database\Query\Processors\PostgresProcessor
     */
    protected function getDefaultPostProcessor()
    {
        return new QueryProcessor();
    }

}
