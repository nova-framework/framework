<?php
/**
 * Structure Manager
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @version 3.0
 * @date January 2nd, 2016
 */

namespace Nova\ORM;

/**
 * Structure helper, will read and cache table and column structures by reading the Annotations.
 *
 * @package Nova\ORM
 */
abstract class Structure
{
    private static $tables = array();

    private static $columns = array();
    
}