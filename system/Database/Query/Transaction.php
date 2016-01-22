<?php

namespace Nova\Database\Query;

use Nova\Database\Query\Builder as BaseBuilder;


class Transaction extends BaseBuilder
{

    /**
     * Commit the database changes
     */
    public function commit()
    {
        $this->pdo->commit();

        throw new TransactionHaltException();
    }

    /**
     * Rollback the database changes
     */
    public function rollback()
    {
        $this->pdo->rollBack();

        throw new TransactionHaltException();
    }
}
