<?php

namespace App\Modules\System\Models;

use Nova\Database\ORM\Model;


class UserLogs extends Model
{
    protected $fillable = array('user_id', 'action', 'action_model', 'action_id');


    public function users()
    {
        return $this->hasOne('App\Modules\Users\User', 'id', 'user_id');
    }
}
