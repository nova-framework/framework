<?php
/**
 * Transaction.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 22th, 2016
 *
 * Based on Pixie Query Builder: https://github.com/usmanhalalit/pixie
 */

namespace Nova\Database\Query\Builder;

use Nova\Database\Query\Builder as BaseBuilder;
use Nova\Database\Query\Builder\TransactionHaltException;


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
