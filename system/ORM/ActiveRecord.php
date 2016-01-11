<?php
/**
 * Expect
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 11th, 2016
 */

namespace Nova\ORM;

use Nova\ORM\Expects;


class ActiveRecord
{
    protected $isNew = true;

    protected $db;

    protected static $cache = array();

    protected $primaryKey = 'id';

    protected $tableName;
    protected $serialize;

    public $belongsTo = array();
    public $hasMany   = array();
    public $hasOne    = array();


    public function __construct()
    {
    }
}
