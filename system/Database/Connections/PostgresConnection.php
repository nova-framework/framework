<?php

namespace Nova\Database\Connections;

use Nova\Database\Connection;
use Nova\Database\Query\Grammars\PostgresGrammar as QueryGrammar;
use Nova\Database\Query\Processors\PostgresProcessor as QueryProcessor;


class PostgresConnection extends Connection
{
    /**
     * Get the default query grammar instance.
     *
     * @return \Nova\Database\Query\Grammars\PostgresGrammar
     */
    protected function getDefaultQueryGrammar()
    {
        return $this->withTablePrefix(new QueryGrammar());
    }

    /**
     * Get the default post processor instance.
     *
     * @return \Nova\Database\Query\Processors\PostgresProcessor
     */
    protected function getDefaultPostProcessor()
    {
        return new QueryProcessor();
    }

}
