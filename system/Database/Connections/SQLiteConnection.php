<?php

namespace Nova\Database\Connections;

use Nova\Database\Connection;
use Nova\Database\Query\Grammars\SQLiteGrammar as QueryGrammar;
use Nova\Database\Query\Processors\SQLiteProcessor as QueryProcessor;


class SQLiteConnection extends Connection
{

    /**
     * Get the default query grammar instance.
     *
     * @return \Nova\Database\Query\Grammars\SQLiteGrammar
     */
    protected function getDefaultQueryGrammar()
    {
        return $this->withTablePrefix(new QueryGrammar());
    }

    /**
     * Get the default post processor instance.
     *
     * @return \Nova\Database\Query\Processors\Processor
     */
    protected function getDefaultPostProcessor()
    {
        return new QueryProcessor();
    }

}
