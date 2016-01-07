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

use FluentPDO;
use FluentStructure;
use PDO;


class QueryBuilder extends FluentPDO;
{

    function __construct(Connection $connection, FluentStructure $structure = null)
    {
        parent::__construct($connection, $structure);
    }
    
}
