<?php
/**
 * Users - A Users Model for being used together with the Database Auth Driver.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Modules\Users\Models;

use Auth\GenericModel;
use Database\Model as BaseModel;


class Users extends BaseModel
{
    protected $table = 'users';

    protected $primaryKey = 'id';


    public function __construct()
    {
        parent::__construct();
    }

    public function updateUser(GenericModel $user)
    {
        $keyName = $this->getKeyName();

        // Retrieve the data from the User Model instance.
        $userId = $user->{$keyName};

        $data = $user->toArray();

        // Unset the primary key.
        unset($data[$keyName]);

        // Update the Database Record.
        $this->where($keyName, $userId)->update($data);
    }
}
