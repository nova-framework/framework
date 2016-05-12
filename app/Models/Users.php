<?php
/**
 * Users - A Users Model.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Models;

use Auth\Model as BaseModel;

use \stdClass;


class Users extends BaseModel
{
    protected $table = 'users';

    protected $primaryKey = 'id';


    public function __construct()
    {
        parent::__construct();
    }

    public function updateUser($id, array $data)
    {
        if($id instanceof stdClass) {
            $userId = $id->{$this->primaryKey};
        } else {
            $userId = intval($id);
        }

        $this->where($this->primaryKey, $userId)->update($data);
    }
}
