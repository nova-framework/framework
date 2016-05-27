<?php
/**
 * Users - A Users Model.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Models\ORM;

use Database\ORM\Model as BaseModel;


class User extends BaseModel
{
    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $hidden = array('password', 'remember_token');
}
