<?php

namespace App\Modules\Demo\Models;

use Nova\ORM\Model as BaseModel;


class User extends BaseModel
{
    protected $table = 'users';

    protected $relations = array('profile', 'posts');


    public function __construct()
    {
        parent::__construct();
    }

    public function profile()
    {
        return $this->hasOne('App\Modules\Demo\Models\Profile', 'user_id');
    }

    public function posts()
    {
        return $this->hasMany('App\Modules\Demo\Models\Post', 'author_id');
    }

}
