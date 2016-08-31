<?php

namespace Database\Connections;

use Database\Connection;
use Database\Query\Grammars\SQLiteGrammar as QueryGrammar;
use Database\Query\Processors\SQLiteProcessor as QueryProcessor;


class SQLiteConnection extends Connection
{

    /**
     * Get the default query grammar instance.
     *
     * @return \Database\Query\Grammars\SQLiteGrammar
     */
    protected function getDefaultQueryGrammar()
    {
        return $this->withTablePrefix(new QueryGrammar);
    }

    /**
     * Get the default post processor instance.
     *
     * @return \Database\Query\Processors\Processor
     */
    protected function getDefaultPostProcessor()
    {
        return new QueryProcessor;
    }

}
