<?php
/**
 * QueryBuilder - Smart SQL builder for Database Connection.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 27th, 2015
 */

namespace Nova\Database;

use Nova\Database\Connection;

use \FluentPDO;
use \FluentStructure;
use \PDO;

/**
 * Query Builder (FluentPDO)
 */
class QueryBuilder extends FluentPDO
{

    /**
     * FluentPDO QueryBuilder constructor.
     * @param \Nova\Database\Connection $connection
     * @param FluentStructure|null $structure
     */
    public function __construct(Connection $connection, FluentStructure $structure = null)
    {
        parent::__construct($connection, $structure);
    }
}
