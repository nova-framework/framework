<?php
/**
 * QueryCounter - Lightweight SQL Logging Tool which count the SQL Queries.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 2th, 2016
 */

namespace Nova\DBAL\Logging;

use Doctrine\DBAL\Logging\SQLLogger;


class QueryCounter implements SQLLogger
{
    private $queryCounter = 0;


    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        $this->queryCounter++;
    }

    /**
     * {@inheritdoc}
     */
    public function stopQuery()
    {
    }


    public function getNumQueries()
    {
        return $this->queryCounter;
    }

}
