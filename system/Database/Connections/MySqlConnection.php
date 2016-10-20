<?php

namespace Nova\Database\Connections;

use Nova\Database\Connection;
use Nova\Database\Query\Grammars\MySqlGrammar as QueryGrammar;
use Nova\Database\Query\Processors\MySqlProcessor as QueryProcessor;


class MySqlConnection extends Connection
{

    /**
     * Get the default query grammar instance.
     *
     * @return \Nova\Database\Query\Grammars\MySqlGrammar
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
