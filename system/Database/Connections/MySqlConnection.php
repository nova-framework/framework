<?php

namespace Database\Connections;

use Database\Connection;
use Database\Query\Grammars\MySqlGrammar as QueryGrammar;
use Database\Query\Processors\MySqlProcessor as QueryProcessor;


class MySqlConnection extends Connection
{

    /**
     * Get the default query grammar instance.
     *
     * @return \Database\Query\Grammars\MySqlGrammar
     */
    protected function getDefaultQueryGrammar()
    {
        return $this->withTablePrefix(new QueryGrammar());
    }

    /**
     * Get the default post processor instance.
     *
     * @return \Database\Query\Processors\Processor
     */
    protected function getDefaultPostProcessor()
    {
        return new QueryProcessor();
    }

}
