<?php
/**
 * Model
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 11th, 2016
 */

namespace Nova\ORM;

use Nova\ORM\Expects;
use Nova\ORM\ActiveRecord;


class Entity extends ActiveRecord
{

    public function __construct()
    {
        parent::__construct();
    }
}
