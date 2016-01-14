<?php

namespace App\Modules\Demo\Models;

use Nova\ORM\Model as BaseModel;


class Profile extends BaseModel
{
    protected $tableName = 'profiles';

    protected $relations = array('user');


    public function __construct()
    {
        parent::__construct();
    }

    public function user()
    {
        return $this->belongsTo('\App\Modules\Demo\Models\User', 'user_id');
    }

}
