<?php
/**
 * Model - An default Users Model for Auth.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Auth;

use Core\Config;
use Database\Model as BaseModel;


class Model extends BaseModel
{
    public function __construct(array $config = array())
    {
        if(is_null($this->table) && ! empty($config)) {
            // No Table name specified, prepare it from configuration.
            $this->table = $config['table'];

            // Adjust the column/key names.
            $this->primaryKey = $config['primaryKey'];
        }

        parent::__construct();
    }
}
