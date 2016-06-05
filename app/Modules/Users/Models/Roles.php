<?php
/**
 * Roles - A Roles Model for being used together with the Database Auth Driver.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Modules\Users\Models;

use Database\Model as BaseModel;


class Roles extends BaseModel
{
    protected $table = 'roles';

    protected $primaryKey = 'id';


    public function __construct()
    {
        parent::__construct();
    }

}
