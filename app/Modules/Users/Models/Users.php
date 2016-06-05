<?php
/**
 * Users - A Users Model for being used together with the Database Auth Driver.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Modules\Users\Models;

use Core\Config;
use Auth\GenericUser;
use Database\Model as BaseModel;

use App\Modules\Users\Models\Roles;

use DB;


class Users extends BaseModel
{
    protected $table = null;

    protected $primaryKey = 'id';


    public function __construct()
    {
        // Configure the Model's table.
        if($this->table === null) {
            $this->table = Config::get('auth.table');
        }

        parent::__construct();
    }

    public function updateGenericUser(GenericUser $user)
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
